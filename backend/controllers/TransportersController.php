<?php


namespace backend\controllers;


use backend\models\Transporters;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class TransportersController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Transporters::find()->where(['=', 'transporter_parent', 0])
                ->orWhere(['transporter_parent' => null])
        ]);
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Transporters();

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
        ], 'Thêm đơn vị vận chuyển', parent::footer());
    }

    /**
     * @param $id
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = Transporters::findOne($id);
        if (!$model) {
            throw new BadRequestHttpException('không tìm thấy trang!');
        }
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
        ], 'Thêm đối tác vận chuyển', parent::footer());
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $transporter = Transporters::findOne($id);
        if (!$transporter) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model = new Transporters();
        try {
            $model->transporter_parent = $id;
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            return static::responseSuccess($exception->getMessage());
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Transporters::find()->where(['transporter_parent' => $id])
        ]);

        return static::responseRemote('view.blade', [
            'transporter' => $transporter,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ], 'Đối tác vận chuyển', parent::footer());
    }

    public function actionDelete($id)
    {
        $model = Transporters::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }
}