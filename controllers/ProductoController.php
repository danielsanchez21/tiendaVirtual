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
        $id =filter_input(INPUT_POST, 'pdt_id', FILTER_SANITIZE_NUMBER_INT);

        $producto = empty($id) ? new Producto() : Producto::findOne($id);

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
            array(
                'db' => 'est_producto',
                'dt' => 5,
                'formatter' => function ($d, $row) {
                    // Botón Editar
                    $buts = '<button type="button" id="editar_plan_' . $row['id_Producto'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalProducto"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_Producto'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button> ';

                    // Botón Activar/Desactivar
                    if ($d == 1) {
                        $buts .= '<button id="estado_desactivar_pdt_' . $row['id_Producto'] . '" 
                        data-id="' . $row['id_Producto'] . '" data-estado="0" 
                        class="btn btn-sm btn-success"
                        title="Desactivar">
                          <i class="fa-solid fa-check"></i>                       
                      </button>';
                    } else {
                        $buts .= '<button id="estado_activar_pdt_' . $row['id_Producto'] . '" 
                        data-id="' . $row['id_Producto'] . '" data-estado="1" 
                        class="btn btn-sm btn-danger"
                        title="Activar">
                     <i class="fa-solid fa-xmark"></i>
                      </button>';
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

    public function actionEstadoproducto()
    {

        $idproducto = $_POST['producto'];
        $pdtestdo = $_POST['estado_producto'];
        $data = Producto::findOne($idproducto);
        $data->est_producto = $pdtestdo;
        $data->update();
    }

    public function actionObtenerproducto()
    {
        $idproducto = $_POST['idproducto'];
        $data = Producto::findOne($idproducto);
        echo json_encode($data->attributes);

    }
}
