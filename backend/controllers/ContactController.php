<?php


namespace backend\controllers;


use backend\models\Contacts;

class ContactController extends BaseController
{
    public function actionIndex(){
        $model = new Contacts();
        return $this->render('index.blade',[
            'model' => $model
        ]);
    }
    public function actionCreate(){

    }
    public function actionDelete(){

    }

}