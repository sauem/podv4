<?php


namespace backend\controllers;


use backend\models\Products;
use backend\models\Warehouse;
use backend\models\WarehouseSearch;
use backend\models\WarehouseTransaction;
use common\helper\Helper;
use PHPUnit\TextUI\Help;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class WarehouseController extends BaseController
{
    function actionIndex()
    {
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        $warehouse_query = Products::find()
            ->innerJoin('categories C', 'C.id = products.category_id')
            ->leftJoin('warehouse_transaction WT', 'WT.product_sku = products.sku')
            ->leftJoin('orders_contact_sku as OCS', 'products.sku = OCS.sku')
            ->innerJoin('orders_contact as OC', 'OCS.order_id = OC.id')
            ->addSelect([
                'C.name',
                'products.sku as sku',
                'products.category_id',
                'WT.transaction_type as type',
                'SUM(CASE WHEN WT.transaction_type = "import" THEN WT.qty END) as import',
                'SUM(CASE WHEN WT.transaction_type = "export" THEN WT.qty END) as export',
                'SUM(CASE WHEN WT.transaction_type = "refund" THEN WT.qty END) as refund',
                'SUM(CASE WHEN WT.transaction_type = "broken" THEN WT.qty END) as broken'
            ])
            ->groupBy('products.category_id')
            ->asArray()->all();

        $product_warehouse = new ArrayDataProvider([
            'allModels' => $warehouse_query,
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
        $warehouse = Warehouse::findOne($warehouse_id);
        if (!$warehouse) {
            return static::responseSuccess(Helper::firstError($warehouse));
        }

        $model = new WarehouseTransaction();

        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {

            $model->transaction_type = strtolower($type);
            if (!$model->save()) {
                return static::responseSuccess(Helper::firstError($model));
            }
            return static::responseSuccess();
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
        $warehouse = Warehouse::findOne($warehouse_id);
        if (!$warehouse) {
            return static::responseSuccess(Helper::firstError($warehouse));
        }

        $model = new WarehouseTransaction();
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {

            $model->transaction_type = strtolower($type);
            if (!$model->save()) {
                return static::responseSuccess(Helper::firstError($model));
            }
            return static::responseSuccess();
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