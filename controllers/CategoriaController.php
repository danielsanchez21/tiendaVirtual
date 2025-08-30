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
        $categoria->nom_categoria = 'ff';
        $categoria->des_categoria = 'ff';
        $categoria->abreviatura = 'c';

        if (!$categoria->validate()) {
            throw new \Exception('Error al crear categoría');
        }

        $categoria->save();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Categoría creada correctamente'];
    }

    public function actionListarcategorias()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Categoria::find()->all();
    }
}
