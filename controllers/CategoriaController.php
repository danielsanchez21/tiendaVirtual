<?php

namespace app\controllers;

use app\helpers\datatables;
use yii\web\Controller;
use yii\web\Response;
use app\models\Categoria;
require_once(\Yii::getAlias('@app/components/SSP.php'));
class CategoriaController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionCrearcategoria()
    {
        $id =filter_input(INPUT_POST,"categoria_id", FILTER_SANITIZE_NUMBER_INT);
        $categoria = empty($id)?new Categoria():Categoria::findOne($id);
        $categoria->nom_categoria = filter_input(INPUT_POST,"categoria-nom_categoria", FILTER_SANITIZE_STRING);
        $categoria->des_categoria = filter_input(INPUT_POST,"categoria-des_categoria", FILTER_SANITIZE_STRING);
        $categoria->abreviatura = filter_input(INPUT_POST,"categoria-abreviatura", FILTER_SANITIZE_STRING);

        if (!$categoria->validate()) {
            throw new \Exception('Error al crear categoría');
        }

        $categoria->save();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=>true,'message' => 'Categoría creada correctamente'];
    }

    public function actionListarcategorias()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Categoria::find()->all();
    }
    public function actionListacategoria()
    {
        $columns = array(
            array('db' => 'id_categoria', 'dt' => 0),
            array('db' => 'nom_categoria', 'dt' => 1),
            array('db' => 'des_categoria', 'dt' => 2),
            array('db' => 'abreviatura', 'dt' => 3),
            array(
                'db' => 'estado',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    // Botón Editar
                    $buts = '<button type="button" id="editar_plan_' . $row['id_categoria'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalcategoria"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_categoria'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button> ';

                    // Botón Activar/Desactivar
                    if ((int)$d === 1) {
                        $buts .= '<button id="estado_desactivar_categoria_' . $row['id_categoria'] . '" 
                        data-id="' . $row['id_categoria'] . '" data-estado="0" 
                        class="btn btn-sm btn-success"
                        title="Desactivar">
                          <i class="fa-solid fa-check"></i>                       
                      </button>';
                    } else {
                        $buts .= '<button id="estado_activar_categoria_' . $row['id_categoria'] . '" 
                        data-id="' . $row['id_categoria'] . '" data-estado="1" 
                        class="btn btn-sm btn-danger"
                        title="Activar">
                         <i class="fa-solid fa-xmark"></i>
                      </button>';
                    }

                    return $buts; // 👈 se devuelve en UNA sola columna
                }
            )
        );

        $primaryKey = "id_categoria";
        $table = "categoria";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    public function actionEstadocategoria()
    {

        $idcategoria = $_POST['categoria'];
        $estado_categoria = $_POST['estado_categoria'];
        $data = Categoria::findOne($idcategoria);
        $data->estado = $estado_categoria;
        $data->update();
    }

    public function actionObtenercategoria()
    {
        $idcategoria = $_POST['idcategoria'];
        $data = Categoria::findOne($idcategoria);
        echo json_encode($data->attributes);

    }
}
