<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\Orders;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use backend\models\OrderStatus;
use backend\models\WarehouseTransaction;
use common\helper\Helper;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class OrderController extends BaseController
{
    public function actionIndex()
    {

        $contactOrder = new OrdersContactSearch();
        $waitShippingOrder = $contactOrder->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_NEW]
            ]
        ]));
        $pendingShippingOrder = $contactOrder->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PENDING]
            ]
        ]));
        $statusShippingOrder = $contactOrder->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [
                    OrdersContact::STATUS_SHIPPED,
                    OrdersContact::STATUS_SHIPPING,
                    OrdersContact::STATUS_REFUND,
                    OrdersContact::STATUS_CANCEL,
                    OrdersContact::STATUS_UNCROSS,
                    OrdersContact::STATUS_CROSSED,
                    OrdersContact::STATUS_PAYED,
                    OrdersContact::STATUS_UNPAID
                ]
            ]
        ]));

        return $this->render('index.blade', [
            'waitShippingOrder' => $waitShippingOrder,
            'pendingShippingOrder' => $pendingShippingOrder,
            'statusShippingOrder' => $statusShippingOrder,
            'searchModel' => $contactOrder
        ]);
    }

    public function actionPending()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PENDING]
            ]
        ]));
        $dataProvider->sort = false;
        return self::responseRemote('tabs/pending.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionStatus()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [
                    OrdersContact::STATUS_SHIPPED,
                    OrdersContact::STATUS_SHIPPING,
                    OrdersContact::STATUS_REFUND,
                    OrdersContact::STATUS_CANCEL,
                    OrdersContact::STATUS_UNCROSS,
                    OrdersContact::STATUS_CROSSED,
                    OrdersContact::STATUS_PAYED,
                    OrdersContact::STATUS_UNPAID
                ]
            ]
        ]));
        $dataProvider->setSort(false);
        return self::responseRemote('tabs/status.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @param $code
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $model = new OrderStatus();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        $ids = \Yii::$app->request->get('ids');
        if (empty($ids)) {
            return static::responseSuccess(0, 1);
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                if (isset($model->ids) && !empty($model->ids)) {
                    $ids = explode(',', $model->ids);
                    foreach ($ids as $id) {
                        $order = OrdersContact::findOne($id);
                        $order->status = OrdersContact::STATUS_PENDING;
                        $order->transport_id = $model->transport_id;
                        $order->warehouse_id = $model->warehouse_id;
                        $order->sub_transport_id = $model->sub_transport_id;
                        $order->order_time = time();
                        if (!$order->save()) {
                            throw new BadRequestHttpException(Helper::firstError($order));
                        }
                        #WarehouseTransaction::addNewHistories($order);
                    }
                    $transaction->commit();
                    return static::responseSuccess();
                }
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('danger', $exception->getMessage());
            }
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
            'ids' => implode(',', $ids),
        ], 'Tạo đơn vận chuyển', $this->footer(),'md');
    }

    /**
     * @param $code
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($code)
    {
        $model = OrdersContact::findOne(['code' => $code]);
        if (!$model) {
            throw new NotFoundHttpException("Không tìm thấy trang!");
        }
        return $this->render('view.blade', [
            'model' => $model
        ]);
    }

    public function actionUpdateTrackingId($code)
    {
        $model = OrdersContact::findOne(['code' => $code]);

        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy mã đơn hàng!');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess();
            }
            \Yii::$app->session->setFlash('danger', Helper::firstError($model));
        }
        return static::responseRemote('input.blade', [
            'model' => $model
        ], 'Tracking ID ' . $model->code, $this->footer());
    }
}