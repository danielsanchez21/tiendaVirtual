<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Persona;
use yii\web\Response;
class PersonaController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearpersona() {
        $persona = new Persona();
        $persona->nombre = filter_input(INPUT_POST,"persona-nombre", FILTER_SANITIZE_STRING);
        $persona->apellido = filter_input(INPUT_POST,"persona-apellido", FILTER_SANITIZE_STRING);
        $persona->num_documento = filter_input(INPUT_POST,"numero_documento", FILTER_SANITIZE_STRING);
        $persona->id_documento = filter_input(INPUT_POST,"tipo_documento", FILTER_SANITIZE_NUMBER_INT);
        $persona->telefono = filter_input(INPUT_POST,"persona-telefono", FILTER_SANITIZE_STRING);
        $persona->direccion = filter_input(INPUT_POST,"persona-direccion", FILTER_SANITIZE_STRING);
        $persona->genero = filter_input(INPUT_POST,"persona-genero", FILTER_SANITIZE_STRING);

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
