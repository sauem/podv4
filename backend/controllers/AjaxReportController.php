<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class AjaxReportController extends BaseController
{
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionSales()
    {

        $query = Contacts::find()
            ->addSelect([
                'SUM(IF( contacts.status != "duplicate", 1, 0)) as C3',
                'SUM(IF( contacts.status = "ok", 1, 0 )) as C8',
                'SUM(IF( contacts.status = "cancel", 1, 0 )) as C6',
                'SUM(IF( contacts.status = "callback" OR contacts.status = "pending", 1, 0 )) as C7',
                'SUM(IF( contacts.status = "number_fail", 1, 0 )) as C4',
                'SUM(IF( contacts.status = "new", 1, 0 )) as C0',
                'FROM_UNIXTIME(contacts.updated_at, \'%d/%m/%Y\') day',
            ])->groupBy('day');

        if (\Yii::$app->request->isPost) {
            $filter = \Yii::$app->request->post();
            $marketer = ArrayHelper::getValue($filter, 'marketer', []);
            $product = ArrayHelper::getValue($filter, 'product', []);
            $source = ArrayHelper::getValue($filter, 'source', []);
            $time_register = ArrayHelper::getValue($filter, 'time_register', '');

        }
        return $query->asArray()->all();
    }

}