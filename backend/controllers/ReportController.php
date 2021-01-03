<?php


namespace backend\controllers;


use backend\models\ContactsSource;
use backend\models\MediaObj;
use backend\models\OrdersTopup;
use backend\models\OrdersTopupSearch;
use backend\models\Products;
use backend\models\UserRole;
use common\helper\Helper;
use yii\db\Transaction;

class ReportController extends BaseController
{
    function actionSales()
    {
        $sources = ContactsSource::LISTS();
        $products = Products::LISTS();
        $marketers = UserRole::LISTS(UserRole::ROLE_MARKETER);
        $sales = UserRole::LISTS(UserRole::ROLE_SALE);


        return $this->render('sales.blade', [
            'sources' => $sources,
            'products' => $products,
            'marketers' => $marketers,
            'sales' => $sales,
        ]);
    }

    function actionFinancial()
    {
        return static::responseRemote("financial.blade");
    }

    function actionCrossed()
    {
        return static::responseRemote("tabs/crossed.blade");

    }

    function actionUncross()
    {
        return static::responseRemote("tabs/uncross.blade");

    }

    function actionTopup()
    {
        $searchModel = new OrdersTopupSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return static::responseRemote("tabs/topup.blade", [
            'dataProvider' => $dataProvider
        ]);
    }

    function actionCreateTopup()
    {
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        $model = new OrdersTopup();

        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    if ($model->thumb) {
                        MediaObj::saveObject($model->thumb, $model->id, MediaObj::OBJECT_TOPUP);
                    }
                    $transaction->commit();
                    return static::responseSuccess(0, 1,'Thêm topup thành công!');
                }
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return static::responseSuccess(0, 0, $exception->getMessage());
        }

        return static::responseRemote("create-topup.blade", [
            'model' => $model
        ], 'Thêm topup', $this->footer());
    }

    function actionHistories()
    {
        return static::responseRemote("tabs/histories.blade");

    }

}