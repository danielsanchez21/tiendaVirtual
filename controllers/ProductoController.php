<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Producto;

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
        return ['message' => 'Producto creado correctamente'];
    }

    public function actionListarproductos()
    {
       $lista = Producto::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }
}
