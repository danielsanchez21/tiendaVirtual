<?php

namespace app\helpers;

use app\models;
use Yii;

/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

class datatables
{


    static function data_output3($columns, $data)
    {
        $out = array();
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();
            $j = 0;
            for ($jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                $temp = explode(".", $column['db']);
                if (count($temp) >= 2) {
                    $column['db'] = $temp[1];
                }

                $temp = explode(" as ", $column['db']);
                if (count($temp) >= 2) {
                    $column['db'] = $temp[1];
                }

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$column['db']];
                }
            }
            //Al final de cada columna agregue botón de editar
            $out[] = $row;
        }
        return $out;
    }


    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit3($request, $columns)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }
        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    static function order3($request, $columns)
    {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = '' . $column['db'] . ' ' . $dir;
                }
            }
            if(count($orderBy) > 0) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }
        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    static function filter3($request, $columns, &$bindings, $table)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {
                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $bindings[':' . $column['db']] = '%' . $str . '%';
                    $globalSearch[] = $column['db'] . " LIKE " . ':' . $column['db'];
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                    $str != '') {
                    $type = "string";
                    if (intval($str) > 0) {
                        $type = "integer";
                    }

                    $column['db'] = explode(" as ", $column['db'])[0];

                    if ($type == "integer") {
                        //$bindings[':' . $column['db']] = $str;
                        $columnSearch[] = $column['db'] . " = " . $str;
                    } else {
                        $bindings[':' . $column['db']] = '%' . $str . '%';
                        $columnSearch[] = $column['db'] . " LIKE '%" .$str."%'";
                    }
                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    //$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        return $where;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */

    static function data_output2($columns, $data)
    {
        $out = array();
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();
            $j = 0;
            for ($jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                $temp = explode(".", $column['db']);
                if (count($temp) >= 2) {
                    $column['db'] = $temp[1];
                }

                $temp = explode(" as ", $column['db']);
                if (count($temp) >= 2) {
                    $column['db'] = $temp[1];
                }

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$column['db']];
                }
            }
            //Al final de cada columna agregue botón de editar
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit2($request, $columns)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }
        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    static function order2($request, $columns)
    {
        $order = ' ';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] =  $column['db'] ." ". $dir;
                }
            }
            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }
        //Yii::log('', CLogger::LEVEL_ERROR, $order);
        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    static function filter2($request, $columns, &$bindings, $table)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {
                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $bindings[':' . $column['db']] = '%' . $str . '%';
                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . ':' . $column['db'];
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                    $str != '') {
                    $type = "string";
                    if (intval($str) > 0) {
                        $type = "integer";
                    }

                    $column['db'] = explode(" as ", $column['db'])[0];

                    if ($type == "integer") {
                        //$bindings[':' . $column['db']] = $str;
                        $columnSearch[] = $column['db'] . " = " . $str;
                    } else {
                        $bindings[':' . $column['db']] = '%' . $str . '%';
                        $columnSearch[] = "" . $column['db'] . " LIKE '%" .$str."%'";
                    }

                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    //$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        return $where;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    static function data_output($columns, $data)
    {
        $out = array();
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();
            $j = 0;
            for ($jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];
                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }
            //Al final de cada columna agregue botón de editar
            $row[$j] = "<button class='btn btn-info' onclick='editRow(" . $data[$i][$columns[0]['db']] . ")'>" . Yii::t("app", "EDITAR") . "</button>";
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit($request, $columns)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }
        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    static function order($request, $columns)
    {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = '`' . $column['db'] . '` ' . $dir;
                }
            }
            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }
        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    static function filter($request, $columns, &$bindings, $table)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {
                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $bindings[':' . $column['db']] = '%' . $str . '%';
                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . ':' . $column['db'];
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                    $str != '') {

                    /**
                     * Consultamos el nombre del modelo...
                     */
                    $mName = explode("_", $table);
                    $modelName = "";
                    for ($j = 0; $j < count($mName); $j++) {
                        $modelName .= ucfirst($mName[$j]);
                    }
                    $var = "app\models\\" . $modelName;
                    $model = new $var();
                    $type = $model->getTableSchema()->columns[$column['db']]->type;

                    if ($type == "integer") {
                        $bindings[':' . $column['db']] = $str;
                        $columnSearch[] = "`" . $column['db'] . "` = " . ':' . $column['db'];
                    } else {
                        $bindings[':' . $column['db']] = '%' . $str . '%';
                        $columnSearch[] = "`" . $column['db'] . "` LIKE " . ':' . $column['db'];
                    }

                    //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    //$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    static function simple($request, $table, $primaryKey, $columns, $group = "")
    {
        $connection = Yii::$app->getDb();
        $bindings = array();

        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings, $table);

        $sql = "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "` FROM `$table` $where $group $order $limit";

        $data = $connection->createCommand($sql, $bindings)->queryAll();

        $resFilterLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
        FROM   `$table`
        $where", $bindings)->queryAll();

        $recordsFiltered = $resFilterLength[0]['total'];
        // Total data set length

        $resTotalLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
             FROM   `$table`")->queryAll();

        $recordsTotal = $resTotalLength[0]['total'];
        /*
         * Output
         */
        return array(
            "draw" => isset($request['draw']) ?
                intval($request['draw']) :
                0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $data)
        );
    }

    static function simple2($request, $table, $primaryKey, $columns, $group = "", $join, $whereIn, $where = true, $extraBindings = [])
    {
        $connection = Yii::$app->getDb();
        $bindings = $extraBindings;
        //$db = self::db( $conn );
        //$db = $conn;
        // Build the SQL query string from the request
        $limit = self::limit3($request, $columns);
        $order = self::order3($request, $columns);
        // Se inicializa la variable $where con el resultado de una expresión.
        // Si $where es falso (o nulo), $where se establece en nulo; de lo contrario, se llama a la función self::filter3().
        $where = !$where ? null : self::filter3($request, $columns, $bindings, $table);

        // Se realiza una verificación para construir la cláusula WHERE.
        if ($where == null || $whereIn == "") {
            // Si $where es nulo o $whereIn es una cadena vacía,
            // se verifica si $whereIn no es nulo y se construye la cláusula WHERE con $whereIn.
            if ($whereIn != null) {
                $where = " WHERE " . $whereIn;
            }
        } else {
            // En el caso de que ni $where sea nulo ni $whereIn sea una cadena vacía,
            // se concatena la cláusula WHERE existente con la cadena $whereIn usando el operador AND.
            $where .= " AND " . $whereIn;
        }
        $sql = "SELECT " . implode(", ", self::pluck($columns, 'db')) . " FROM $table $join $where $group $order $limit";

        $command = $connection->createCommand($sql);
        foreach ($bindings as $key => $value) {
            $command->bindValue($key, $value);
        }
        $data = $command->queryAll();

        $commandFilter = $connection->createCommand("SELECT COUNT({$primaryKey}) as total
         FROM   $table $join $where");

        foreach ($bindings as $key => $value) {
            $commandFilter->bindValue($key, $value);
        }
        $resFilterLength = $commandFilter->queryAll();

        $recordsFiltered = $resFilterLength[0]['total'];
        // Total data set length

        $comTotalLength = $connection->createCommand("SELECT COUNT({$primaryKey}) as total
             FROM   $table $join $where");
        foreach ($bindings as $key => $value) {
            $comTotalLength->bindValue($key, $value);
        }
        $resTotalLength = $comTotalLength->queryAll();

        $recordsTotal = $resTotalLength[0]['total'];
        /*
         * Output
         */
        return array(
            "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output2($columns, $data)
        );
    }

    /**
     * The difference between this method and the `simple` one, is that you can
     * apply additional `where` conditions to the SQL queries. These can be in
     * one of two forms:
     *
     * * 'Result condition' - This is applied to the result set, but not the
     *   overall paging information query - i.e. it will not effect the number
     *   of records that a user sees they can have access to. This should be
     *   used when you want apply a filtering condition that the user has sent.
     * * 'All condition' - This is applied to all queries that are made and
     *   reduces the number of records that the user can access. This should be
     *   used in conditions where you don't want the user to ever have access to
     *   particular records (for example, restricting by a login id).
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @param  string $whereResult WHERE condition to apply to the result set
     *  @param  string $whereAll WHERE condition to apply to all queries
     *  @return array          Server-side processing response array
     */
    static function complex($request, $table, $primaryKey, $columns, $whereResult = null, $whereAll = null, $group = "",$extraBindings = [])
    {
        $connection = Yii::$app->getDb();
        $bindings = $extraBindings;
        //$db = self::db($conn);
        $localWhereResult = array();
        $localWhereAll = array();
        $whereAllSql = '';
        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings, $table);
        $whereResult = self::_flatten($whereResult);
        $whereAll = self::_flatten($whereAll);
        if ($whereResult) {
            $where = $where ?
                $where . ' AND ' . $whereResult :
                'WHERE ' . $whereResult;
        }
        if ($whereAll) {
            $where = $where ?
                $where . ' AND ' . $whereAll :
                'WHERE ' . $whereAll;
            $whereAllSql = 'WHERE ' . $whereAll;
        }
        // Main query to actually get the data
        /*$data = self::sql_exec($db, $bindings, "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "`
             FROM `$table`
             $where
             $order
             $limit"
        );*/
        $sql = "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "` FROM `$table` $where $group $order $limit";
        $data = $connection->createCommand($sql, $bindings)->queryAll();



        // Data set length after filtering

        $resFilterLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
        FROM   `$table`
        $where", $bindings)->queryAll();

        $recordsFiltered = $resFilterLength[0]['total'];
        // Total data set length

        $resTotalLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
             FROM   `$table`".$whereAllSql)->queryAll();

        $recordsTotal = $resTotalLength[0]['total'];

        /*
        $resFilterLength = self::sql_exec($db, $bindings, "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table`
             $where"
        );
        $recordsFiltered = $resFilterLength[0][0];
        // Total data set length
        $resTotalLength = self::sql_exec($db, $bindings, "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table` " .
                        $whereAllSql
        );
        $recordsTotal = $resTotalLength[0][0];*/



        /*
         * Output
         */
        return array(
            "draw" => isset($request['draw']) ?
                intval($request['draw']) :
                0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $data)
        );
    }

    static function complex2($request, $table, $primaryKey, $columns, $whereResult = null, $whereAll = null, $group = "", $join = "")
    {
        $connection = Yii::$app->getDb();
        $bindings = array();
        //$db = self::db($conn);
        $localWhereResult = array();
        $localWhereAll = array();
        $whereAllSql = '';
        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings, $table);
        $whereResult = self::_flatten($whereResult);
        $whereAll = self::_flatten($whereAll);
        if ($whereResult) {
            $where = $where ?
                $where . ' AND ' . $whereResult :
                'WHERE ' . $whereResult;
        }
        if ($whereAll) {
            $where = $where ?
                $where . ' AND ' . $whereAll :
                'WHERE ' . $whereAll;
            $whereAllSql = 'WHERE ' . $whereAll;
        }
        // Main query to actually get the data
        /*$data = self::sql_exec($db, $bindings, "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "`
             FROM `$table`
             $where
             $order
             $limit"
        );*/
        $sql = "SELECT dtr_estado, distribuidor.dtr_id, dtr_tipo_doc, dtr_num_doc, dtr_nombre, usuario_creador, distribuidor.dtr_id FROM `$table` $join $where $group $order $limit";
        $data = $connection->createCommand($sql, $bindings)->queryAll();



        // Data set length after filtering

        $resFilterLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
        FROM   `$table`
        $where", $bindings)->queryAll();

        $recordsFiltered = $resFilterLength[0]['total'];
        // Total data set length

        $resTotalLength = $connection->createCommand("SELECT COUNT(`{$primaryKey}`) as total
             FROM   `$table`".$whereAllSql)->queryAll();

        $recordsTotal = $resTotalLength[0]['total'];

        /*
        $resFilterLength = self::sql_exec($db, $bindings, "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table`
             $where"
        );
        $recordsFiltered = $resFilterLength[0][0];
        // Total data set length
        $resTotalLength = self::sql_exec($db, $bindings, "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table` " .
                        $whereAllSql
        );
        $recordsTotal = $resTotalLength[0][0];*/



        /*
         * Output
         */
        return array(
            "draw" => isset($request['draw']) ?
                intval($request['draw']) :
                0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $data)
        );
    }

    /*     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal($msg)
    {
        echo json_encode(array(
            "error" => $msg
        ));
        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind(&$a, $val, $type)
    {
        $key = ':binding_' . count($a);
        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );
        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    static function pluck($a, $prop)
    {
        $out = array();
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }

    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    static function _flatten($a, $join = ' AND ')
    {
        if (!$a) {
            return '';
        } elseif ($a && is_array($a)) {
            return implode($join, $a);
        }
        return $a;
    }
}
