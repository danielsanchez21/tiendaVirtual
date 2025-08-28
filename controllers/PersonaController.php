<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Persona;

class PersonaController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearpersona() {
        $persona = new Persona();
        $persona->nombre = 'Juan';
        $persona->apellido = 'Pérez';
        $persona->id_documento = 1;
        $persona->telefono = '3001234567';
        $persona->direccion = 'Calle 123';
        $persona->genero = 'M';

        if (!$persona->validate()) {
            throw new Exception('Error al crear persona');
        }

        $persona->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Persona creada correctamente'];
    }

    public function actionListarpersonas() {
        $lista = Persona::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }
}
