<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Rol;

class RolController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearrol() {
        $rol = new Rol();
        $rol->nombre = 'Administrador';

        if (!$rol->validate()) {
            throw new Exception('Error al crear rol');
        }

        $rol->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Rol creado correctamente'];
    }

    public function actionListarroles() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return  Rol::find()->all();

    }
}
