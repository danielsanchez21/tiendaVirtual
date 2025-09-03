<?php

namespace app\controllers;

use app\helpers\datatables;
use yii\web\Controller;
use yii\web\Response;
use app\models\Producto;
require_once(\Yii::getAlias('@app/components/SSP.php'));

class ProductoController extends Controller
{
    public $enableCsrfValidation = false; // ⚠️ solo para pruebas con Postman o AJAX sin token

    public function actionCrearproducto()
    {
        $producto = new Producto();
        $producto->nombre =filter_input(INPUT_POST, 'nombreproducto', FILTER_SANITIZE_STRING);
        $producto->descrip_producto = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $producto->stock = filter_input(INPUT_POST, 'existencias', FILTER_SANITIZE_NUMBER_INT);;
        $producto->precio_costo = filter_input(INPUT_POST, 'pdtcosto', FILTER_SANITIZE_NUMBER_INT);;
        $producto->precio_venta = filter_input(INPUT_POST, 'pdtventa', FILTER_SANITIZE_NUMBER_INT);;
        $producto->fk_categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_NUMBER_INT);;

        if (!$producto->validate()) {
            throw new \Exception('Error al crear producto');
        }

        $producto->save();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=>true,'message' => 'Producto creado correctamente'];
    }

    public function actionListarproductos()
    {
       $lista = Producto::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }
    public function actionListaproductos()
    {
        $columns = array(
            array('db' => 'id_Producto', 'dt' => 0),
            array('db' => 'nombre', 'dt' => 1),
            array('db' => 'stock', 'dt' => 2),
            array('db' => 'precio_costo', 'dt' => 3),
            array('db' => 'precio_costo', 'dt' => 4),
            array('db' => 'id_Producto', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    $buts = '<button type="button" id="editar_plan_' . $row['id_Producto'] . '" 
                    title="editar" data-toggle="modal" data-target="#modal-actualizar"
                    class="mfb-component__button--child tooltipedit bg-blue waves waves-effect"
                    data-id="' . $row['id_Producto'] . '"></button>';

                    if ($d == 1) {
                        $buts .= "<button id='estado_desactivar_plan_" . $row['id_Producto'] . "' 
                        data-id='" . $row['id_Producto'] . "' data-estado='0' 
                        class='mfb-component__button--child bg-green waves waves-effect' 
                        title='Estado'>
                        </button>";
                    } else {
                        $buts .= "<button id='estado_activar_plan_" . $row['id_Producto'] . "' 
                        data-id='" . $row['id_Producto'] . "' data-estado='1' 
                        class='mfb-component__button--child bg-red waves waves-effect' 
                        title='Estado'>
                        </button>";
                    }
                    return $buts;
                }),

        );
        //Indice
        $primaryKey = "id_Producto";
        //Tabla
        $table = "producto";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);
        echo json_encode($result);


    }
}
