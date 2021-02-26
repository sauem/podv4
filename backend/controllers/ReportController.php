<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsSearch;
use backend\models\ContactsSource;
use backend\models\MediaObj;
use backend\models\OrdersContact;
use backend\models\OrdersTopup;
use backend\models\OrdersTopupSearch;
use backend\models\Products;
use backend\models\UserRole;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Transaction;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\ArrayHelper;

class ReportController extends BaseController
{
    function actionSales()
    {
        $sources = ContactsSource::LISTS();
        $products = Products::LISTS();
        $marketers = UserRole::LISTS(UserRole::ROLE_MARKETER);
        $sales = UserRole::LISTS(UserRole::ROLE_SALE, true);


        return $this->render('sales.blade', [
            'sources' => $sources,
            'products' => $products,
            'marketers' => $marketers,
            'sales' => $sales,
        ]);
    }

    function actionFinancial()
    {
        $products = Products::LISTS();
        $marketers = UserRole::LISTS(UserRole::ROLE_MARKETER);
        $sales = UserRole::LISTS(UserRole::ROLE_SALE, true);
        
        return static::responseRemote("financial.blade", [
            'products' => $products,
            'sales' => $sales,
            'marketers' => $marketers
        ]);
    }

    function actionCrossed()
    {
        $query = OrdersContact::find()
            ->with(['contact', 'skuItems'])
            ->from('orders_contact as O')
            ->where(['O.status' => OrdersContact::STATUS_CROSSED])
            ->orWhere(['O.shipping_status' => OrdersContact::STATUS_REFUND])
            ->addSelect([
                'id',
                'name',
                'code',
                'phone',
                'cross_status',
                'cross_check_code',
                'remittance_date',
                'transport_fee',
                'collection_fee',
                'total_bill',
                'payment_status',
                'shipping_status',
                'status',
                'SUM(IF(O.payment_status = "paid" AND O.cross_status = "crossed", total_bill ,0)) as total_crossed',
                'SUM(IF(O.payment_status = "paid" AND O.cross_status = "crossed", total_bill, 0)) as C11',
                'SUM(IF(O.shipping_status = "refund" AND O.cross_status = "crossed", transport_fee, 0)) as refund_transport_fee'
            ])->groupBy(['O.code']);
        $query = $query->asArray()->all();
        $data = static::getData($query);

        $C11 = array_sum(ArrayHelper::getColumn($data, 'C11'));
        $transport_fee = array_sum(ArrayHelper::getColumn($data, 'transport_fee'));
        $collection_fee = array_sum(ArrayHelper::getColumn($data, 'collection_fee'));
        $C13 = array_sum(ArrayHelper::getColumn($data, 'C13'));
        $service_fee = array_sum(ArrayHelper::getColumn($data, 'service_fee'));
        $refund_transport_fee = ArrayHelper::getValue($data, 'refund_transport_fee', 0);

        $dataProvider = new ArrayDataProvider([
            'allModels' => Helper::isEmpty($data) ? [] : $data
        ]);

        return static::responseRemote("tabs/crossed.blade", [
            'dataProvider' => $dataProvider,
            'C11' => $C11,
            'transport_fee' => $transport_fee + $refund_transport_fee,
            'collection_fee' => $collection_fee,
            'C13' => $C13,
            'service_fee' => $service_fee
        ]);
    }

    static function getData($query, $status = 'total_crossed')
    {
        return array_map(function ($item) use ($status) {
            $C11 = ArrayHelper::getValue($item, 'C11', 0);
            $total_bill = ArrayHelper::getValue($item, $status, 0);
            $collection_fee = ArrayHelper::getValue($item, 'collection_fee', 0);
            $transport_fee = ArrayHelper::getValue($item, 'transport_fee', 0);
            $service_fee = ArrayHelper::getValue($item, 'contact.partner.service_fee', 18);
            $service_fee = Helper::calculate($service_fee, $C11, true);

            return array_merge($item, [
                'service_fee' => $service_fee,
                'C13' => $total_bill - $collection_fee - $transport_fee - $service_fee,
            ]);
        }, $query);
    }

    function actionUncross()
    {
        $query = OrdersContact::find()
            ->with(['contact', 'skuItems'])
            ->from('orders_contact as O')
            ->where(['O.payment_status' => OrdersContact::STATUS_PAYED])
            ->orWhere(['O.shipping_status' => OrdersContact::STATUS_REFUND])
            ->andWhere(['IS', 'O.cross_status', new Expression('NULL')])
            ->addSelect([
                'id',
                'name',
                'code',
                'phone',
                'cross_status',
                'cross_check_code',
                'remittance_date',
                'transport_fee',
                'collection_fee',
                'total_bill',
                'payment_status',
                'shipping_status',
                'status',
                'SUM(IF(O.payment_status = "paid" AND O.cross_status IS NULL, total_bill ,0)) as total_uncross',
                'SUM(IF(O.payment_status = "paid" AND O.cross_status IS NULL, total_bill, 0)) as C11',
                'SUM(IF(O.shipping_status = "refund" AND O.cross_status IS NULL, transport_fee, 0)) as refund_transport_fee'
            ])->groupBy(['O.code']);
        $query = $query->asArray()->all();
        $data = static::getData($query, 'total_uncross');

        $C11 = array_sum(ArrayHelper::getColumn($data, 'C11'));
        $transport_fee = array_sum(ArrayHelper::getColumn($data, 'transport_fee'));
        $collection_fee = array_sum(ArrayHelper::getColumn($data, 'collection_fee'));
        $C13 = array_sum(ArrayHelper::getColumn($data, 'C13'));
        $service_fee = array_sum(ArrayHelper::getColumn($data, 'service_fee'));

        $refund_uncross = ArrayHelper::getValue($data, 'refund_uncross', 0);

        $dataProvider = new ArrayDataProvider([
            'allModels' => Helper::isEmpty($data) ? [] : $data
        ]);


        return static::responseRemote("tabs/uncross.blade", [
            'dataProvider' => $dataProvider,
            'C11' => $C11,
            'transport_fee' => $transport_fee + $refund_uncross,
            'collection_fee' => $collection_fee,
            'C13' => $C13,
            'service_fee' => $service_fee
        ]);
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
                    return static::responseSuccess(0, 1, 'Thêm topup thành công!');
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
