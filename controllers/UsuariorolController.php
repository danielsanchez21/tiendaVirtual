<?php

namespace app\controllers;

use app\helpers\datatables;
use app\models\Rol;
use app\models\Usuario;
use app\models\UsuarioRol;
use Yii;
use yii\web\Controller;
use yii\web\Response;
require_once(\Yii::getAlias('@app/components/SSP.php'));

class UsuariorolController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionCrearusuariorol() {

        $idUserRol = filter_input(INPUT_POST,"id_user_rol",FILTER_SANITIZE_NUMBER_INT);
        $usuarioRol = empty($idUserRol) ? new UsuarioRol() : UsuarioRol::findOne($idUserRol);

        $usuarioRol->id_usuario = filter_input(INPUT_POST,"id_usuario", FILTER_SANITIZE_NUMBER_INT);
        $usuarioRol->id_rol = filter_input(INPUT_POST,"id_rol", FILTER_SANITIZE_NUMBER_INT);

        if (!$usuarioRol->validate()) {
            throw new \Exception('Error al asignar el rol');
        }

        $usuarioRol->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=>true,'message'=>'Rol guardado correctamente'];
    }

    public function actionListarusuariorol()
    {
        $columns = array(
            array('db' => 'id_user_rol', 'dt' => 0),
            array('db' => 'id_usuario', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $info = Usuario::find()->where(['id_usuario' => $d])->one();
                    return !empty($info) ? $info->nombre: '';
                }
            ),
            array('db' => 'id_rol', 'dt' => 2,
                'formatter'=>function($d, $row) {
                    $info = Rol::find()->where(['id_rol' => $d])->one();
                    return !empty($info)? $info->nombre : '';
                }
            ),
            array(
                'db' => 'id_user_rol',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    $buts = '<button type="button" id="editar_usuario_' . $row['id_user_rol'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalusuariorol"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_user_rol'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button>  ';


                    return $buts;
                }),
        );

        $primaryKey = "id_user_rol";
        $table = "usuario_rol";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionObtenerusuariorol()
    {
        $id = $_POST['id_user_rol'];
        $data = UsuarioRol::findOne($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $data->attributes;
    }

}