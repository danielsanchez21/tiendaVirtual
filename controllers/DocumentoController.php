<?php
namespace app\controllers;
use app\helpers\datatables;
use Yii;
use yii\web\Controller;
use app\models\Documento;
use yii\web\Response;

class DocumentoController extends Controller {

    public $enableCsrfValidation = false;
    public function actionCreardocumento() {
        $documento = new Documento();
        $documento->nombre = filter_input(INPUT_POST,"rol-nombre", FILTER_SANITIZE_STRING);
        $documento->abreviatura = filter_input(INPUT_POST,"documento-abreviatura", FILTER_SANITIZE_STRING);

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
                    $buts = '<button type="button" 
                            class="btn btn-sm btn-primary editar-documento"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalDocumento"
                            data-id="' . $row['id_documento'] . '"
                            title="Editar">
                            <i class="fa-regular fa-pen-to-square"></i>
                         </button> ';

                    // Botón Activar / Desactivar
                    if ((int)$d === 1) {
                        $buts .= '<button 
                                class="btn btn-sm btn-success cambiar-estado-documento"
                                data-id="' . $row['id_documento'] . '" 
                                data-estado="0" 
                                title="Desactivar">
                                <i class="fa-solid fa-check"></i>
                              </button>';
                    } else {
                        $buts .= '<button 
                                class="btn btn-sm btn-danger cambiar-estado-documento"
                                data-id="' . $row['id_documento'] . '" 
                                data-estado="1" 
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

}
