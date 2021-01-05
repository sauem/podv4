<?php


namespace backend\controllers;


use backend\models\OrderCrossStatus;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use backend\models\OrderStatus;
use common\helper\Helper;
use yii\db\Transaction;

class OrderCrossController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PAYED, OrdersContact::STATUS_REFUND]
            ]
        ]));

        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCrossed()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_CROSSED],
                'cross_status' => [OrdersContact::STATUS_CROSSED],
            ]
        ]));

        return static::responseRemote('crossed.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionMakeCross()
    {
        $model = new OrderCrossStatus();
        $ids = \Yii::$app->request->get('codes');
        if (empty($ids)) {
            return static::responseSuccess(0, 1);
        }

        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search([
            'OrdersContactSearch' => [
                'id' => $ids,
//                'payment_status' => [OrdersContact::STATUS_PAYED, OrdersContact::STATUS_REFUND],
//                'status' => OrdersContact::STATUS_PAYED
            ]
        ]);
        return static::responseRemote("make-cross.blade", [
            "model" => $model,
            "dataProvider" => $dataProvider,
        ], "Đối Soát", $this->footer(), 'full');
    }
}