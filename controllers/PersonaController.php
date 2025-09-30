<?php
namespace app\controllers;
require_once(\Yii::getAlias('@app/components/SSP.php'));

use app\helpers\datatables;
use app\models\Documento;
use Yii;
use yii\web\Controller;
use app\models\Persona;
use yii\web\Response;
class PersonaController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearpersona() {
        $id= filter_input(INPUT_POST,"id_persona", FILTER_SANITIZE_NUMBER_INT);
        $persona = empty($id) ? new Persona() : Persona::findOne($id);
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
        return ['success'=>true,'message' => 'Persona creada correctamente'];
    }

    public function actionListarpersonas() {
        $lista = Persona::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }

    public function actionListapersonas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $columns = array(
            array('db' => 'id_persona', 'dt' => 0),
            array('db' => 'id_persona', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $info = Persona::find()->where(['id_persona' => $d])->one();
                    return !empty($info) ? $info->nombre . " " . $info->apellido : '';
                }
            ),
            array('db' => 'id_documento', 'dt' => 2,
                'formatter' => function ($d, $row) {
                    $doc = Documento::findOne($d);
                    return empty($doc) ? '' : $doc->nombre;
                }
            ),
            array('db' => 'num_documento', 'dt' => 3),
            array('db' => 'direccion', 'dt' => 4),
            array('db' => 'telefono', 'dt' => 5),
            array('db' => 'id_persona', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return '<button type="button" id="editar_persona_' . $row['id_persona'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalclientes"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_persona'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button>';
                }
            )
        );

        $primaryKey = "id_persona";
        $table = "persona";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);

        return $result;
    }

    public function actionObtenerpersona()
    {
        $id= $_POST['idpersona'];
        $data = Persona::findOne($id);
        echo json_encode($data->attributes);
    }
}
