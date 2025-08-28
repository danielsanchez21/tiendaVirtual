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
        $producto->nombre = 'Portátil Lenovo';
        $producto->descrip_producto = 'Laptop de alto rendimiento';
        $producto->stock = 10;
        $producto->precio_costo = 2000000;
        $producto->precio_venta = 2500000;
        $producto->fk_categoria = 1;

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
        return $lista;
    }
}
