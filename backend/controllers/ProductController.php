<?php


namespace backend\controllers;


use backend\models\MediaObj;
use backend\models\Products;
use backend\models\ProductsPrice;
use backend\models\ProductsSearch;
use common\helper\Helper;
use yii\db\Transaction;
use yii\web\NotFoundHttpException;

class ProductController extends BaseController
{
    public function actionIndex()
    {

        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate()
    {
        $model = new Products();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    if ($model->thumb) {
                        MediaObj::saveObject($model->thumb, $model->id, MediaObj::OBJECT_PRODUCT);
                    }
                    ProductsPrice::savePrice($model->sku, \Yii::$app->request->post('prices'));
                    $transaction->commit();
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('warning', $exception->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
        ], 'Thêm sản phẩm', parent::footer());
    }

    /**
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {
        $model = Products::findOne($id);
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    MediaObj::saveObject($model->thumb, $model->id, MediaObj::OBJECT_PRODUCT);
                    ProductsPrice::removePrice($model->sku);
                    ProductsPrice::savePrice($model->sku, \Yii::$app->request->post('prices'));
                    $transaction->commit();
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return static::responseSuccess($exception->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
        ], 'Cập nhật sản phẩm', parent::footer());
    }

    public function actionDelete($id)
    {
        $model = Products::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }
}