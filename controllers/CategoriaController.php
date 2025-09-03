<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Categoria;

class CategoriaController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionCrearcategoria()
    {
        $categoria = new Categoria();
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
}
