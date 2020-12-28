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
        $codes = \Yii::$app->request->get('codes');
        if (empty($codes)) {
            return static::responseSuccess(0, 1);
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if (!$model->save()) {
                return static::responseSuccess(0, 0, Helper::firstError($model));
            }
        }
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search([
            'OrdersContactSearch' => [
                'code' => $codes,
                'status' => [OrdersContact::STATUS_PAYED, OrdersContact::STATUS_REFUND]
            ]
        ]);
        return static::responseRemote("make-cross.blade", [
            "model" => $model,
            "dataProvider" => $dataProvider,
        ], "Đối Soát", $this->footer(), 'full');
    }
}