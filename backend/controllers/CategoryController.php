<?php


namespace backend\controllers;


use backend\models\Categories;
use backend\models\CategoriesSearch;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CategoryController extends BaseController
{
    public function actionIndex()
    {
        $model = new Categories();

        $searchModel = new CategoriesSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);


        return $this->render('index.blade', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Categories();
        $model->country = \Yii::$app->cache->get('country');
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            return static::responseSuccess($exception->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Thêm loại sản phẩm', parent::footer());
    }

    public function actionUpdate($id)
    {
        $model = Categories::findOne($id);

        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            return static::responseSuccess($exception->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Cập nhật loại sản phẩm', parent::footer());
    }

    public function actionDelete($id)
    {
        $model = Categories::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }

}