<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return boolean
     */
    public function actionIndex()
    {
        echo true;
    }

    /**
     * Redireccion a un evento especifico...
     *
     * @return string
     */
    public function actionEvento()
    {

        echo "index.html";
    }


    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays busqueda page.
     *
     * @return string
     */
    public function actionBusqueda()
    {
        return $this->render('busqueda');
    }
}
