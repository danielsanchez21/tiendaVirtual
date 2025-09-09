<?php
namespace app\controllers;
use app\helpers\datatables;
use Yii;
use yii\web\Controller;
use app\models\Documento;
use yii\web\Response;
require_once(\Yii::getAlias('@app/components/SSP.php'));
class DocumentoController extends Controller {

    public $enableCsrfValidation = false;
    public function actionCreardocumento() {
        $id = filter_input(INPUT_POST,'documento_id',FILTER_SANITIZE_NUMBER_INT);
        $documento =empty($id)? new Documento():Documento::findOne($id);
        $documento->nombre = filter_input(INPUT_POST,"nombre_documento", FILTER_SANITIZE_STRING);
        $documento->abreviatura = filter_input(INPUT_POST,"abreviatura", FILTER_SANITIZE_STRING);

        if (!$documento->validate()) {
            throw new Exception('Error al crear documento');
        }

        $documento->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success'=>true,'message' => 'Tipo documento creada correctamente'];
    }

    public function actionListardocumentos() {
        $lista = Documento::find()->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }
    public function actionListadocumentos()
    {
        $columns = [
            ['db' => 'id_documento', 'dt' => 0],
            ['db' => 'nombre', 'dt' => 1],
            ['db' => 'abreviatura', 'dt' => 2],
            [
                'db' => 'estado',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    // Botón Editar
                    $buts = '<button type="button" id="editar_plan_' . $row['id_documento'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modaldocumento"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_documento'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button> ';

                    // Botón Activar / Desactivar
                    if ((int)$d === 1) {
                        $buts .= '<button id="estado_desactivar_doc_' . $row['id_documento'] . '" 
                        data-id="' . $row['id_documento'] . '" data-estado="0" 
                        class="btn btn-sm btn-success"
                        title="Desactivar">
                          <i class="fa-solid fa-check"></i>                       
                      </button>';
                    } else {
                        $buts .= '<button id="estado_activar_doc_' . $row['id_documento'] . '" 
                        data-id="' . $row['id_documento'] . '" data-estado="1" 
                        class="btn btn-sm btn-danger"
                        title="Activar">
                     <i class="fa-solid fa-xmark"></i>
                      </button>';
                    }
                    return $buts;
                }
            ]
        ];

        // Índice
        $primaryKey = "id_documento";
        // Tabla
        $table = "documento";

        // Ejecutar consulta DataTables
        $result = datatables::simple($_POST, $table, $primaryKey, $columns);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }
 public function actionEstado()
 {
     $id = $_POST['documento_id'];
     $estado = $_POST['estado_documento'];
     $data = Documento::findOne($id);
     $data->estado = $estado;
     $data->update();
 }

    public function actionObtenerdocumento() {
        $id =$_POST['id_documento'];
        $data =Documento::findOne($id);
        echo json_encode($data->attributes);
    }


}
