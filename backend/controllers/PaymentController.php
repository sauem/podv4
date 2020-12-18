<?php


namespace backend\controllers;


use backend\models\Payments;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class PaymentController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Payments::find()
        ]);
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Payments();
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess();
            }
            \Yii::$app->session->setFlash('danger', Helper::firstError($model));
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Thêm hình thức thanh toán', $this->footer());
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = Payments::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tồn tại hình thức thanh toán này!');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess();
            }
            \Yii::$app->session->setFlash('danger', Helper::firstError($model));
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Sửa hình thức thanh toán', $this->footer());
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
        $model = Payments::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }
}