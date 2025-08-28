<?php

use yii\web\Controller;
use app\models\Documento;

class DocumentoController extends Controller {

    public function actionCreardocumento() {
        $documento = new Documento();
        $documento->nombre = 'Cédula';
        $documento->abreviatura = 'CC';

        if (!$documento->validate()) {
            throw new Exception('Error al crear documento');
        }

        $documento->save();
        return json_encode(['message' => 'Documento creado correctamente']);
    }

    public function actionListarDocumentos() {
        $lista = Documento::find()->all();
        return json_encode($lista);
    }
}
