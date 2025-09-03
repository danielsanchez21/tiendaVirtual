<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Usuario;
use yii\web\Response;
class UsuarioController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearusuario() {
        $usuario = new Usuario();
        $usuario->nombre = filter_input(INPUT_POST,"usuario-nombre", FILTER_SANITIZE_STRING);
        $usuario->correo = filter_input(INPUT_POST,"usuario-correo", FILTER_SANITIZE_STRING);
        $usuario->contraseña = filter_input(INPUT_POST,"usuario-contraseña", FILTER_SANITIZE_STRING);

        if (!$usuario->validate()) {
            throw new Exception('Error al crear usuario');
        }

        $usuario->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Usuario creado correctamente'];
    }

    public function actionListarusuarios() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Usuario::find()->all();
    }
}
