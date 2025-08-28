<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Usuario;

class UsuarioController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearusuario() {
        $usuario = new Usuario();
        $usuario->nombre = 'Carlos';
        $usuario->correo = 'carlos@example.com';
        $usuario->contraseña = password_hash('123456', PASSWORD_BCRYPT);

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
