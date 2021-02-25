<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsAssignment;
use backend\models\ContactsLogStatus;
use backend\models\Media;
use backend\models\OrdersContact;
use backend\models\OrdersContactSku;
use backend\models\OrdersExample;
use backend\models\Products;
use backend\models\ProductsPrice;
use backend\models\ProductsSearch;
use backend\models\Transporters;
use backend\models\UploadForm;
use backend\models\WarehouseHistories;
use backend\models\WarehouseStorage;
use backend\models\ZipcodeCountry;
use common\helper\Helper;
use yii\base\Exception;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxController extends BaseController
{
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * @return \backend\models\Media|bool
     * @throws BadRequestHttpException
     */
    function actionUploadFile()
    {
        $model = new UploadForm();
        if (\Yii::$app->request->isPost) {

            try {
                $model->load(\Yii::$app->request->post(), "");
                return $model->upload();
            } catch (Exception $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        }
        throw new BadRequestHttpException("Post only");
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    function actionRemoveFile()
    {
        $model = Media::findOne(['url' => \Yii::$app->request->post('url')]);
        try {
            if (!$model) {
                throw new NotFoundHttpException('Không tìm thấy ảnh!');
            }
            if (file_exists(UPLOAD_PATH . str_replace('static/', '', $model->url))) {
                unlink(UPLOAD_PATH . str_replace('static/', '', $model->url));
                $model->delete();
            }
        } catch (\Exception $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
        return true;
    }

    /**
     * @throws BadRequestHttpException
     */
    function actionAssignPhone()
    {
        $phones = \Yii::$app->request->post('phones');
        $saleID = \Yii::$app->request->post('saleID');

        try {
            if (!$phones) {
                throw new BadRequestHttpException('không có số điện hoại nào!');
            }
            $data = array_map(function ($item) use ($saleID) {
                return array_merge($item, [
                    'user_id' => $saleID,
                    'status' => ContactsAssignment::STATUS_PENDING
                ]);
            }, $phones);
            foreach ($data as $value) {
                $model = ContactsAssignment::findOne(['phone' => $value['phone']]);
                if (!$model) {
                    $model = new ContactsAssignment();
                }
                $model->load($value, '');
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
            }

//            \Yii::$app->db->createCommand()->batchInsert(
//                ContactsAssignment::tableName(),
//                ['phone', 'country', 'user_id', 'status', 'created_at', 'updated_at'],
//                $data)
//                ->execute();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return true;
    }

    /**
     * @return array|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     */
    function actionInfoProduct()
    {
        $sku = Products::find()->where([
            'products.sku' => \Yii::$app->request->post('sku')])
            ->orWhere(['products.id' => \Yii::$app->request->post('sku')])
            //->with('category')
            ->with('partner')
            ->with('media')
            ->asArray()->one();
        if (!$sku) {
            throw new BadRequestHttpException('Không tìm thấy sản phẩm!');
        }
        return [
            'id' => $sku['id'],
            'sku' => $sku['sku'],
            'media' => $sku['media']['media']['url'],
            'name' => $sku['category']['name'] . '-' . $sku['sku'],
            'size' => $sku['size'],
            'weight' => $sku['weight'],
            'prices' => []
        ];
    }

    function actionGetListProduct($code = null)
    {
        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        if ($code) {
            $contact = Contacts::findOne(['code' => $code]);
            if ($contact) {
                $dataProvider->query->andFilterWhere(['categories.name' => $contact->category]);
            }
        }
        $dataProvider->query->with(['media', 'category', 'partner']);
        return $dataProvider->getModels();
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionChangeStatus()
    {
        $model = \Yii::$app->request->post('model');
        $status = \Yii::$app->request->post('status');
        $key = \Yii::$app->request->post('ids');
        $reason = \Yii::$app->request->post('reason');
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);

        try {
            $model = new \ReflectionClass("backend\models\\$model");
            $model = $model->newInstanceWithoutConstructor();
            $model::updateAll(['status' => $status], ['id' => $key]);

            if (is_array($key)) {
                foreach ($key as $id) {
                    $contact = Contacts::findOne($id);
                    if (!$contact) {
                        throw new BadRequestHttpException("Không tìm thấy liên hệ!");
                    }
                    ContactsLogStatus::saveRecord($contact->code, $contact->phone, $status, $reason);
                }
            } else {
                $contact = Contacts::findOne($key);
                if (!$contact) {
                    throw new BadRequestHttpException("Không tìm thấy liên hệ!");
                }
                ContactsLogStatus::saveRecord($contact->code, $contact->phone, $status);
            }
            $new = ContactsAssignment::completeAssignment(ContactsAssignment::getPhoneAssign());
            #$new = ContactsAssignment::nextAssignment();
            $transaction->commit();
            return [
                'success' => 1,
                'assigned' => !$new ? 0 : 1,
                'msg' => $new ? 'Số mới được áp dụng' : 'Thao tác thành công!'
            ];
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function actionGetPrice()
    {
        $sku = \Yii::$app->request->post("sku");
        $qty = \Yii::$app->request->post("qty");
        $model = ProductsPrice::findOne(['sku' => $sku, 'qty' => $qty]);
        if (!$model) {
            throw new BadRequestHttpException("Không tìm thấy giá mặc định!");
        }
        return [
            'price' => $model->price,
            'qty' => $model->qty,
            'sku' => $model->sku
        ];
    }

    /**
     * @return mixed|null
     * @throws BadRequestHttpException
     */
    public function actionGetTransport()
    {
        $model = Transporters::findOne(\Yii::$app->request->post('transportID'));
        if (!$model) {
            throw new BadRequestHttpException("Không có đơn vị vận chuyển nào!");
        }
        return $model->children;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionGetZipcode()
    {
        $zipcode = \Yii::$app->request->post('zipcode');
        $model = ZipcodeCountry::findOne(['zipcode' => $zipcode, 'code' => \Yii::$app->cache->get('country')]);
        if (!$model) {
            throw new BadRequestHttpException(Helper::firstError($model));
        }
        return ArrayHelper::toArray($model);
    }

    /**
     * @return array|array[]|object|object[]|string|string[]
     * @throws NotFoundHttpException
     */
    public function actionGetOrderExample()
    {
        $code = \Yii::$app->request->post('code');
        $contact = Contacts::findOne(['code' => $code]);
        if (!$contact) {
            throw new NotFoundHttpException("Không tìm thấy liên hệ!");
        }
        $model = OrdersExample::find()
            ->with(['skuItems'])
            ->where(['option' => $contact->option, 'category' => $contact->category])
            ->orWhere(['option' => $contact->option])
            ->asArray()->one();
        if (!$model) {
            throw new NotFoundHttpException("Không có mẫu đơn phù hợp!");
        }
        return $model;
    }

    /**
     * @throws BadRequestHttpException
     */

    public function actionWarehouseAvailable()
    {
        try {
            $codes = \Yii::$app->request->post('codes');
            // $codes = ['#CCVN0002317'];
            $inventory = [];
            $skuItems = OrdersContactSku::find()->
            innerJoin('orders_contact', 'orders_contact.id = orders_contact_sku.order_id')
                ->addSelect([
                    'orders_contact_sku.sku',
                    'SUM(qty) as needed'
                ])->where(['orders_contact.code' => $codes])
                ->groupBy(['orders_contact_sku.sku'])
                ->asArray()->all();

            $storage = WarehouseStorage::find()
                ->innerJoin('warehouse', 'storage.warehouse_id = warehouse.id')
                ->innerJoin('products', 'products.sku = storage.sku')
                ->leftJoin('categories', 'products.category_id = categories.id')
                ->from('warehouse_storage as storage')
                ->addSelect([
                    'warehouse.name as warehouse',
                    'categories.name as category',
                    'storage.sku',
                    'SUM(storage.qty) as inventory'
                ])->groupBy(['products.category_id', 'products.sku'])
                ->asArray()->all();
            $histories = WarehouseHistories::find()
                ->from('warehouse_histories as histories')
                ->innerJoin('products', 'products.sku = histories.product_sku')
                ->addSelect([
                    'histories.product_sku as sku',
                    'SUM(IF(histories.transaction_type = "output" , histories.qty , 0)) as minus'
                ])
                ->groupBy(['histories.product_sku'])
                ->asArray()->all();
            if (!Helper::isEmpty($storage) || !Helper::isEmpty($histories)) {
                $inventory = array_map(function ($item1, $item2) {
                    return array_merge($item1, !Helper::isEmpty($item2) ? $item2 : []);
                }, $storage, $histories);
            }

            $inventory = array_map(function ($item) {
                return array_merge($item, [
                    'inventory' => $item['inventory'] - (isset($item['minus']) ? $item['minus'] : 0)
                ]);
            }, $inventory);

            return $inventory;
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function actionChangeLanguage()
    {
        $code = \Yii::$app->request->post('lang');
        \Yii::$app->cache->set('language', $code);
        return $code;
    }

    public function actionSumTotal()
    {
        $codes = \Yii::$app->request->get('codes');
        $orders = OrdersContact::find()
            ->where(['code' => $codes])
            ->addSelect([
                'SUM( shipping_cost) as shipping_total',
                'SUM( total_bill) as total_amount',
            ])->asArray()->all();
        if (!$orders) {
            return false;
        }
        return $orders[0];
    }
}
