<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsSearch;
use common\helper\Helper;

class ContactController extends BaseController
{
    public function actionIndex()
    {
        $model = new Contacts();
        $searchModel = new ContactsSearch();
        $allContact = $searchModel->search(\Yii::$app->request->post());

        $waitingContact = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [Contacts::STATUS_NEW]
            ]
        ]));
        $waitingContact->query->groupBy('phone');

        return $this->render('index.blade', [
            'model' => $model,
            'searchModel' => $searchModel,
            'allContact' => $allContact,
            'waitingContact' => $waitingContact
        ]);
    }

    public function actionCreate()
    {

    }
}