<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;

class OrderController extends BaseController
{
    public function actionIndex()
    {

        $contactOrder = new OrdersContactSearch();

        $waitShippingOrder = $contactOrder->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => Contacts::STATUS_NEW
            ]
        ]));

        return $this->render('index.blade', [
            'waitShippingContact' => $waitShippingOrder
        ]);
    }

    public function actionCreate()
    {
        $model = new OrdersContact();
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Tạo đơn vận chuyển', $this->footer());
    }

}