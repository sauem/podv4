<?php


namespace backend\controllers;


use backend\models\Categories;
use backend\models\Contacts;
use backend\models\OrdersContact;
use backend\models\OrdersContactSku;
use backend\models\OrdersExample;
use backend\models\OrdersExampleItem;
use backend\models\OrdersRefund;
use backend\models\Products;
use backend\models\ProductsPrice;
use backend\models\UserModel;
use backend\models\ZipcodeCountry;
use common\helper\Helper;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
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

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionOrderExample()
    {
        $data = \Yii::$app->request->post('row');
        $model = new OrdersExample();
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            if ($model->load($data, "") && $model->save()) {
                $items = ArrayHelper::getValue($data, 'items', null);
                if (Helper::isEmpty($items)) {
                    throw new BadRequestHttpException("Sản phẩm rỗng!");
                }
                $items = explode(',', $items);
                $items = array_map(function ($item) {
                    $item = explode('*', $item);
                    return [
                        'sku' => $item[0],
                        'qty' => isset($item[1]) ? $item[1] : null,
                        'price' => isset($item[2]) ? $item[2] : 0
                    ];
                }, $items);
                OrdersExampleItem::saveItems($model->id, $items);
                $transaction->commit();
                return true;
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new BadRequestHttpException($exception->getMessage());
        }

    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionCategory()
    {
        $data = \Yii::$app->request->post('row');
        $model = new Categories();
        $partner = UserModel::findOne(['username' => $data['partner']]);
        $data['partner_id'] = $partner ? $partner->getId() : null;
        unset($data['partner']);
        if ($model->load($data, "") && $model->save()) {
            return true;
        }
        throw new BadRequestHttpException(Helper::firstError($model));
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionProduct()
    {
        $data = \Yii::$app->request->post('row');
        $model = new Products();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        try {
            $prices = ArrayHelper::getValue($data, 'prices', null);
            $marketer = UserModel::findOne(['username' => $data['marketer_id']]);
            $category = Categories::findOne(['name' => $data['category_id']]);
            if (!$category) {
                throw new BadRequestHttpException("Không tồn tại loại sản phẩm trên!");
            }
            $model->category_id = $category ? $category->id : null;
            $model->sku = $data['sku'];
            $model->weight = $data['weight'];
            $model->size = $data['size'];
            $model->marketer_id = $marketer ? $marketer->getId() : null;
            $model->marketer_rage_start = $data['marketer_rage_start'];
            $model->marketer_rage_end = $data['marketer_rage_end'];
            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }
            if (!empty($prices)) {
                $prices = explode(',', $prices);
                $prices = array_map(function ($item) use ($model) {
                    $item = explode('*', $item);
                    return [
                        'qty' => isset($item[0]) ? $item[0] : null,
                        'price' => isset($item[1]) ? $item[1] : null
                    ];
                }, $prices);
                ProductsPrice::savePrice($model->sku, $prices);
            }
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new BadRequestHttpException($exception->getMessage());
        }
        return true;
    }

    public function actionOrder()
    {
        $data = \Yii::$app->request->post('row');
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $contact = Contacts::findOne(['code' => $data['code']]);
            if (!$contact) {
                throw new  BadRequestHttpException("Không tìm thấy liên hệ!");
            }
            $items = [];
            for ($i = 1; $i <= 4; $i++) {
                $product = ArrayHelper::getValue($data, "product_$i", null);
                if (!$product) {
                    continue;
                }
                $product = explode('*', $product);
                $items[] = [
                    'sku' => $product[0],
                    'qty' => $product[1],
                    'price' => Helper::toFloat($product[2])
                ];
            }
            $model = new OrdersContact();
            $model->items = $items;
            $model->load($data, '');
            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }
            $orderId = $model->id;
            OrdersContactSku::saveItems($orderId, $items);
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new BadRequestHttpException($exception->getMessage());
        }
        return true;
    }
}