<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Rol;
use yii\web\Response;

class RolController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearrol() {
        $rol = new Rol();
        $rol->nombre = filter_input(INPUT_POST,"rol_nombre", FILTER_SANITIZE_STRING);

        if (!$rol->validate()) {
            throw new Exception('Error al crear rol');
        }

        $rol->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=> true,'message' => 'Rol creado correctamente'];
    }

    public function actionListarroles() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return  Rol::find()->all();

    }
}
