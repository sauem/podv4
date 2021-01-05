<?php


namespace backend\controllers;


use backend\models\ZipcodeCountry;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CountryController extends BaseController
{
    public function actionIndex()
    {
        $model = new ZipcodeCountry();

        $dataProvider = new ActiveDataProvider([
            'query' => ZipcodeCountry::find()
        ]);

        return $this->render('index.blade', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new ZipcodeCountry();

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
        ], 'Thêm quốc gia', parent::footer());
    }

    public function actionUpdate($id)
    {
        $model = ZipcodeCountry::findOne($id);

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
        ], 'Cập nhật quốc gia', parent::footer());
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
        $model = ZipcodeCountry::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }

}