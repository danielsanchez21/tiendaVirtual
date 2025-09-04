<?php
namespace app\controllers;
use app\helpers\datatables;
use yii\web\Controller;
use app\models\Rol;
use yii\web\Response;
require_once(\Yii::getAlias('@app/components/SSP.php'));
class RolController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearrol() {
        $id= filter_input(INPUT_POST,"rol_id", FILTER_SANITIZE_STRING);;
        $rol = empty($id)? new Rol() : Rol::findOne($id);
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

    public function actionListarol()
    {
        $columns = array(
            array('db' => 'id_rol', 'dt' => 0),
            array('db' => 'nombre', 'dt' => 1),
            array(
                'db' => 'estado',
                'dt' => 2,
                'formatter' => function ($d, $row) {
                    // Botón Editar
                    $buts = '<button type="button" id="editar_rol_' . $row['id_rol'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalRol"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_rol'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button> ';

                    // Botón Activar/Desactivar
                    if ($d == 1) {
                        $buts .= '<button id="estado_desactivar_rol_' . $row['id_rol'] . '" 
                        data-id="' . $row['id_rol'] . '" data-estado="0" 
                        class="btn btn-sm btn-success"
                        title="Desactivar">
                          <i class="fa-solid fa-check"></i>                       
                      </button>';
                    } else {
                        $buts .= '<button id="estado_activar_rol_' . $row['id_rol'] . '" 
                        data-id="' . $row['id_rol'] . '" data-estado="1" 
                        class="btn btn-sm btn-danger"
                        title="Activar">
                     <i class="fa-solid fa-xmark"></i>
                      </button>';
                    }
                    return $buts;
                }),

        );
        //Indice
        $primaryKey = "id_rol";
        //Tabla
        $table = "rol";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);
        echo json_encode($result);
    }
    public function actionEstadorol()
    {
        $idrol = $_POST['rol'];
        $estado_rol = $_POST['estado_rol'];
        $data = Rol::findOne($idrol);
        $data->estado = $estado_rol;
        $data->update();
    }
    public function actionObtenerrol()
    {
        $idrol = $_POST['idrol'];
        $data = Rol::findOne($idrol);
        echo json_encode($data->attributes);
    }
}
