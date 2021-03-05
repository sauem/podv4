<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\Orders;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use backend\models\OrderStatus;
use backend\models\WarehouseHistories;
use backend\models\WarehouseStorage;
use backend\models\WarehouseTransaction;
use common\helper\Helper;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends BaseController
{
    public function actionIndex()
    {

        $contactOrder = new OrdersContactSearch();
        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => OrdersContact::STATUS_NEW,
            ]
        ], \Yii::$app->request->queryParams);

        $waitShippingOrder = $contactOrder->search($params);


        $pendingShippingOrder = $contactOrder->search(array_merge_recursive(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PENDING]
            ]
        ]));
        $statusShippingOrder = $contactOrder->search(array_merge_recursive(\Yii::$app->request->queryParams, [
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

    public function actionGetIndex()
    {
        $contactOrder = new OrdersContactSearch();
        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => OrdersContact::STATUS_NEW,
            ]
        ], \Yii::$app->request->queryParams);
        $waitShippingOrder = $contactOrder->search($params);
        return static::responseRemote('tabs/default.blade', [
            'dataProvider' => $waitShippingOrder,
            'searchModel' => $contactOrder
        ], null, null);
    }

    public function actionGetPending()
    {
        $searchModel = new OrdersContactSearch();

        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PENDING]
            ]
        ], \Yii::$app->request->get());
        $dataProvider = $searchModel->search($params);
        $offset = ArrayHelper::getValue($params, 'offset', 0);
        $dataProvider->query->offset($offset);
        $dataProvider->query->limit(20);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = $dataProvider->query->asArray()->all();
        #Helper::printf($dataProvider->query->createCommand()->rawSql);
        return [
            'data' => $data,
            'offset' => ((int)$offset + 20),
            'limit' => 20,
            'shown' => $offset . '/' . $dataProvider->getTotalCount(),
            'max' => sizeof($data) < 20
        ];
    }

    public function actionPending()
    {
        $searchModel = new OrdersContactSearch();

        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_PENDING]
            ]
        ], \Yii::$app->request->queryParams);

        $dataProvider = $searchModel->search($params);

        return self::responseRemote('tabs/pending.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCancel()
    {
        $searchModel = new OrdersContactSearch();

        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_CANCEL]
            ]
        ], \Yii::$app->request->queryParams);

        $dataProvider = $searchModel->search($params);

        return self::responseRemote('tabs/cancel.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionGetStatus()
    {
        $searchModel = new OrdersContactSearch();
        $params = array_merge_recursive([
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
        ], \Yii::$app->request->get());
        $dataProvider = $searchModel->search($params);
        $offset = ArrayHelper::getValue($params, 'offset', 0);
        $dataProvider->query->offset($offset);
        $dataProvider->query->limit(20);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = $dataProvider->query->asArray()->all();
        return [
            'data' => $data,
            'offset' => ((int)$offset + 20),
            'limit' => 20,
            'shown' => $offset . '/' . $dataProvider->getTotalCount(),
            'max' => sizeof($data) < 20
        ];
    }

    public function actionStatus()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge_recursive(\Yii::$app->request->queryParams, [
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

    public function actionGetCancel()
    {
        $searchModel = new OrdersContactSearch();
        $params = array_merge_recursive([
            'OrdersContactSearch' => [
                'status' => [
                    OrdersContact::STATUS_CANCEL,
                ]
            ]
        ], \Yii::$app->request->get());
        $dataProvider = $searchModel->search($params);
        $offset = ArrayHelper::getValue($params, 'offset', 0);
        $dataProvider->query->offset($offset);
        $dataProvider->query->limit(20);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = $dataProvider->query->asArray()->all();
        return [
            'data' => $data,
            'offset' => ((int)$offset + 20),
            'limit' => 20,
            'total' => $dataProvider->getTotalCount(),
            'shown' => $offset . '/' . $dataProvider->getTotalCount(),
            'max' => sizeof($data) < 20
        ];
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
            $model->total_bill = \Yii::$app->request->post("total_bill");
            $model->total_price = Helper::toFloat(\Yii::$app->request->post("total_price"));
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
                        WarehouseTransaction::checkStorage($order);
                        WarehouseHistories::saveHistories(
                            $order->code,
                            $order->skuItems,
                            WarehouseHistories::TYPE_OUTPUT);
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
        ], 'Tạo đơn vận chuyển', $this->footer());
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

    /**
     * @param $code
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdateTrackingId($code)
    {
        $model = OrdersContact::findOne(['code' => $code]);

        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy mã đơn hàng!');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            $model->status = OrdersContact::STATUS_SHIPPING;
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
