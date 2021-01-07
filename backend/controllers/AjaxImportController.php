<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\OrdersContact;
use backend\models\OrdersRefund;
use backend\models\Products;
use backend\models\ZipcodeCountry;
use common\helper\Helper;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AjaxImportController extends BaseController
{
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionContact()
    {
        $data = \Yii::$app->request->post('row');
        $model = new Contacts();
        if ($model->load($data, '')) {
            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionTrackingNumber()
    {
        $data = \Yii::$app->request->post('row');
        $model = OrdersContact::findOne(['code' => $data['code']]);
        if (!$model) {
            throw new BadRequestHttpException('Không tìm thấy đơn hàng!');
        }
        if ($model->load($data, '')) {
            $model->status = OrdersContact::STATUS_SHIPPING;
            $model->shipping_status = OrdersContact::STATUS_SHIPPING;


            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionRefund()
    {
        $data = \Yii::$app->request->post('row');
        $contact = OrdersContact::findOne(['code' => $data['code'], 'checking_number' => $data['checking_number']]);
        $product = Products::findOne(['sku' => $data['sku']]);
        $refund = new OrdersRefund();
        if (!$contact) {
            throw new BadRequestHttpException('Không tìm thấy đơn hàng!');
        }
        if (!$product) {
            throw new BadRequestHttpException('Không tìm thấy sản phẩm!');
        }
        try {
            if ($refund->load($data, '') && $refund->save()) {
                OrdersContact::updateAll([
                    'status' => OrdersContact::STATUS_REFUND,
                    'shipping_status' => OrdersContact::STATUS_REFUND,
                ], ['code' => $refund->code]);
            }
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return Helper::firstError($contact);
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionPaid()
    {
        $data = \Yii::$app->request->post('row');
        $model = OrdersContact::findOne(['code' => $data['code'], 'checking_number' => $data['checking_number']]);
        if (!$model) {
            throw new BadRequestHttpException('Không tìm thấy đơn hàng!');
        }
        $model->cod_cost = $data['cod_cost'];
        $model->time_shipped_success = $data['time_shipped_success'];
        $model->collection_fee = $data['collection_fee'];
        $model->transport_fee = $data['transport_fee'];
        $model->payment_status = OrdersContact::STATUS_PAYED;
        $model->status = OrdersContact::STATUS_PAYED;
        $model->time_paid_success = time();

        if (!$model->save()) {
            throw new BadRequestHttpException(Helper::firstError($model));
        }
        return true;
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionCountry()
    {
        $data = \Yii::$app->request->post('row');
        $model = ZipcodeCountry::findOne([
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'code' => $data['code']]);
        if (!$model) {
            $model = new ZipcodeCountry();
        }
        try {
            if ($model->load($data, "")) {
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
            }
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return true;

    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionCrossed()
    {
        $data = \Yii::$app->request->post('row');
        $model = OrdersContact::findOne(['code' => $data['code']]);
        if (!$model) {
            throw new BadRequestHttpException('Không tìm thấy đơn hàng!');
        }
        if ($model->payment_status !== OrdersContact::STATUS_PAYED || $model->status !== OrdersContact::STATUS_REFUND) {
            throw new BadRequestHttpException('Không có đơn đã thanh toán hoặc hoàn!');
        }
        $model->remittance_date = $data['remittance_date'];
        $model->cross_check_code = $data['cross_check_code'];
        $model->cross_status = OrdersContact::STATUS_CROSSED;
        $model->status = OrdersContact::STATUS_CROSSED;

        if (!$model->save()) {
            throw new BadRequestHttpException(Helper::firstError($model));
        }
        return true;
    }
}