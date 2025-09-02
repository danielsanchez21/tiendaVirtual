<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Documento;
use yii\web\Response;

class DocumentoController extends Controller {

    public $enableCsrfValidation = false;
    public function actionCreardocumento() {
        $documento = new Documento();
        $documento->nombre = filter_input(INPUT_POST,"rol-nombre", FILTER_SANITIZE_STRING);
        $documento->abreviatura = filter_input(INPUT_POST,"documento-abreviatura", FILTER_SANITIZE_STRING);

        if (!$documento->validate()) {
            throw new Exception('Error al crear documento');
        }

        $documento->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=>true,'message' => 'Tipo documento creada correctamente'];
    }

    public function actionListardocumentos() {
        $lista = Documento::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }
}
