<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Factura;

class FacturaController extends Controller
{
    public $enableCsrfValidation = false;

    // Crear factura
    public function actionCrearfactura()
    {
        $factura = new Factura();
        $factura->fecha_factura = date('Y-m-d H:i:s'); // Fecha actual
        $factura->valor_factura = filter_input(INPUT_POST, "valor_factura", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $factura->fk_persona = filter_input(INPUT_POST, "factura-fk_persona", FILTER_SANITIZE_NUMBER_INT);
        if (!$factura->validate()) {
            throw new \Exception('Error al crear factura');
        }

        $factura->save();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Factura creada correctamente'];
    }

    // Listar facturas
    public function actionListarfacturas()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Factura::find()->all();
    }
}
