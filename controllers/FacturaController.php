<?php

namespace app\controllers;

use app\helpers\datatables;
use app\models\FacturaHasProducto;
use app\models\Persona;
use app\models\Producto;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Factura;
require_once(\Yii::getAlias('@app/components/SSP.php'));
class FacturaController extends Controller
{
    public $enableCsrfValidation = false;

    // Crear factura


    public function actionCrearfactura()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post('payload');
        $data = json_decode($data, true);

        $transaction = yii::$app->db->beginTransaction();
        try {

            $factura = new Factura();
            $factura->fecha_factura = date('Y-m-d');
            $factura->valor_factura = $data['total'];
            $factura->fk_persona =1;
            $factura->fk_usuario =1;
            $factura->estado_factura='1';
            if (!$factura->validate()) {
                throw new \Exception('Error al crear factura');
            }
            $factura->save();

            foreach ($data['items'] as $producto) {
                $prod=producto::findOne($producto['product_id']);

                $facturaProductos = new FacturaHasProducto();
                $facturaProductos->id_factura = $factura->id_factura;
                $facturaProductos->id_producto = $prod->id_Producto;
                $facturaProductos->cantidad = $producto['qty'];
                $facturaProductos->valor = $producto['qty']*$prod->precio_venta;
                if(!$facturaProductos->validate()){
                    throw new \Exception('Error al crear factura');
                }
                $facturaProductos->save();

                // actualizacion del inventario
                $prod->stock=$prod->stock-$producto['qty'];
                $prod->update();


            }
            $transaction->commit();

            return ['success' => true, 'message' => 'Factura creada correctamente'];

        }catch (\Exception $e) {

            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // Listar facturas
    public function actionListarfacturas()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Factura::find()->all();
    }

    public function actionReportefactura()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $columns = array(
            array('db' => 'id_factura', 'dt' => 0),
            array('db' => 'fecha_factura', 'dt' => 1),
            array('db' => 'fk_persona', 'dt' => 2,
                'formatter' => function ($d, $row) {
                    $info = Persona::find()->where(['id_persona' => $d])->one();
                    return !empty($info) ? $info->nombre . " " . $info->apellido : '';
                }
            ),
            array('db' => 'valor_factura', 'dt' => 3,
                'formatter' => function ($d, $row) {
                return '$' . number_format($d, 2, '.', ',');
                }),


        );

        $primaryKey = "id_factura";
        $table = "factura";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);

        return $result;
    }
}
