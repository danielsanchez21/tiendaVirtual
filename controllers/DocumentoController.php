<?php

use yii\web\Controller;
use app\models\Documento;
use yii\web\Response;

class DocumentoController extends Controller {

    public function actionCreardocumento() {
        $documento = new Documento();
        $documento->nombre = 'Cédula';
        $documento->abreviatura = 'CC';

        if (!$documento->validate()) {
            throw new Exception('Error al crear documento');
        }

        $documento->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Tipo documento creada correctamente'];
    }

    public function actionListarDocumentos() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Documento::find()->all();
    }
}
