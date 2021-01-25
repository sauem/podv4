<?php


namespace backend\controllers;


use backend\models\Products;
use backend\models\UserRole;
use backend\models\Warehouse;
use backend\models\WarehouseSearch;
use backend\models\WarehouseStorage;
use backend\models\WarehouseTransaction;
use common\helper\Helper;
use PHPUnit\TextUI\Help;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class WarehouseController extends BaseController
{
    function actionIndex()
    {
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $inventory = Products::find()
            ->from('products as P')
            ->leftJoin('warehouse_transaction WT', 'WT.product_sku = P.sku')
            ->addSelect([
                'P.category_id',
                'P.sku',
                'categories.name',
                'WT.qty',
                'WT.transaction_type',
                'SUM(IF(WT.transaction_type = "import", WT.qty, 0)) as import',
                'SUM(IF(WT.transaction_type = "export", WT.qty, 0)) as export',
            ]);


        $sole = Products::find()->from('products as P')
            ->leftJoin('orders_contact_sku OCS', 'OCS.sku = P.sku')
            ->leftJoin('orders_contact OC', 'OC.id = OCS.order_id')
            ->addSelect([
                'P.category_id',
                'P.sku',
                'categories.name',
                'SUM(IF(OC.status = "new", OCS.qty, 0)) as holdup',
                'SUM(IF(OC.payment_status = "paid" OR OC.status = "paid", OCS.qty, 0)) as sole',
                'SUM(IF(OC.payment_status = "refund" OR OC.status = "refund", OCS.qty, 0)) as refund',
                'SUM(IF(OC.status = "broken", OCS.qty, 0)) as broken',
                'SUM(IF(OC.shipping_status = "shipping" OR OC.status = "shipping" OR OC.checking_number IS NOT NULL, OCS.qty, 0)) as shipping',
            ]);
        if (Helper::isRole(UserRole::ROLE_PARTNER)) {
            $sole->where(['P.partner_id' => \Yii::$app->user->getId()]);
            $inventory->where(['P.partner_id' => \Yii::$app->user->getId()]);
        }
        $inventory = $inventory->groupBy(['P.sku'])->asArray()->all();
        $sole = $sole->groupBy(['P.sku'])->asArray()->all();
        $inventory = array_map(function ($item, $item2) {
            return array_merge($item, $item2);
        }, $inventory, $sole);
        $inventory = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'qty' => $item['qty'],
                'import' => $item['import'],
                'export' => $item['export'],
                'sku' => $item['sku'],
                'sole' => $item['sole'],
                'holdup' => $item['holdup'],
                'refund' => $item['refund'],
                'broken' => $item['broken'],
                'shipping' => $item['shipping'],
            ];
        }, $inventory);

        $product_warehouse = new ArrayDataProvider([
            'allModels' => $inventory,
        ]);
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
            'product_warehouse' => $product_warehouse
        ]);
    }

    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $model = new Warehouse();

        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if (!$model->save()) {
                return static::responseSuccess();
            }
            return static::responseSuccess();
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Tạo kho', $this->footer());
    }

    /**
     * @param $id
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionView($id)
    {
        $warehouse = Warehouse::findOne($id);
        if (!$warehouse) {
            throw new BadRequestHttpException(Helper::firstError($warehouse));
        }
        $dataProvider = new ActiveDataProvider([
            'query' => WarehouseTransaction::find()->where([
                'warehouse_id' => $id
            ])
        ]);
        return $this->render('view.blade', [
            'warehouse' => $warehouse,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {
        $model = Warehouse::findOne($id);
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if (!$model->save()) {
                return static::responseSuccess();
            }
            return static::responseSuccess();
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Sửa kho', $this->footer());

    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = Warehouse::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }

    //

    public function actionImportProduct($warehouse_id, $type)
    {

        $model = new WarehouseTransaction();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        $warehouse = Warehouse::findOne($warehouse_id);
        if (!$warehouse) {
            return static::responseSuccess(0, 0, 'Kho không tồn tại!', 'danger');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                $model->transaction_type = strtolower($type);
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                WarehouseStorage::updateStorage(
                    $warehouse_id,
                    $model->product_sku,
                    $model->qty,
                    $model->po_code,
                    WarehouseStorage::TYPE_PLUS
                );
                $transaction->commit();
                return static::responseSuccess(1, 1, 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('danger', $exception->getMessage());
            }
        }

        return static::responseRemote('transaction.blade', [
            'model' => $model,
            'warehouse' => $warehouse,
        ], 'Nhập kho', $this->footer());
    }

    /**
     * @param $warehouse_id
     * @param $type
     * @return array|string
     * @throws \Exception
     */
    public function actionExportProduct($warehouse_id, $type)
    {
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        $warehouse = Warehouse::findOne($warehouse_id);
        $model = new WarehouseTransaction();
        if (!$warehouse) {
            return static::responseSuccess(0, 0, 'Kho không tồn tại!', 'danger');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                $model->transaction_type = strtolower($type);
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                WarehouseStorage::updateStorage(
                    $warehouse_id,
                    $model->product_sku,
                    $model->qty,
                    $model->po_code,
                    WarehouseStorage::TYPE_MINUS
                );
                $transaction->commit();
                return static::responseSuccess(1, 1, 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('danger', $exception->getMessage());
            }
        }
        return static::responseRemote('transaction.blade', [
            'model' => $model,
            'warehouse' => $warehouse,
        ], 'Xuất kho', $this->footer());
    }

    /**
     * @param $id
     * @param $type
     * @return array|string
     */
    public function actionUpdateTransaction($id, $type)
    {
        $model = WarehouseTransaction::findOne($id);
        $warehouse = Warehouse::findOne($model->warehouse_id);

        if (!$model || !$warehouse) {
            return static::responseSuccess(Helper::firstError($model));
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if (!$model->save()) {
                return static::responseSuccess(Helper::firstError($model));
            }
            return static::responseSuccess();
        }
        return static::responseRemote('transaction.blade', [
            'model' => $model,
            'warehouse' => $warehouse,
            'type' => $type,
        ], 'Cập nhật kho', $this->footer());
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteTransaction($id)
    {
        $model = WarehouseTransaction::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }
}
