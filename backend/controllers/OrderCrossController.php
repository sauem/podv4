<?php


namespace backend\controllers;


use backend\models\OrderCrossStatus;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use backend\models\OrderStatus;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;

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
        $dataProvider->setSort(false);
        return static::responseRemote('crossed.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionMakeCross()
    {
        $model = new OrderCrossStatus();
        $ids = \Yii::$app->request->get('codes');
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        if (empty($ids)) {
            return static::responseSuccess(0, 1);
        }

        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                foreach ($ids as $id) {
                    $order = OrdersContact::findOne($id);
                    if (!$order) {
                        throw new BadRequestHttpException("Không tìm thấy đơn hàng!");
                    }
                    $order->cross_check_code = OrderCrossStatus::makeCode(
                        $model->remittance_date,
                        $model->country,
                        $model->partner,
                        $model->cross_count
                    );
                    $order->remittance_date = Helper::timer(str_replace("/", '-', $model->remittance_date));
                    $order->cross_status = OrdersContact::STATUS_CROSSED;
                    $order->status = OrdersContact::STATUS_CROSSED;
                    if (!$order->save()) {
                        throw new BadRequestHttpException(Helper::firstError($order));
                    }
                }
                $transaction->commit();
                return static::responseSuccess(1, 1, 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash("danger", $exception->getMessage());
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => OrdersContact::find()->where(['id' => $ids])
        ]);
        return static::responseRemote("make-cross.blade", [
            "model" => $model,
            "dataProvider" => $dataProvider,
        ], "Đối Soát", $this->footer(), 'full');
    }
}
