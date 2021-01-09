<?php

namespace backend\controllers;

use common\helper\Helper;
use Yii;
use backend\models\ContactsReason;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContactReasonController implements the CRUD actions for ContactsReason model.
 */
class ContactReasonController extends BaseController
{

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ContactsReason::find()
        ]);

        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new ContactsReason();
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                return static::responseSuccess();
            }
        } catch (\Exception $exception) {
            Yii::$app->session->setFlash('warning', $exception->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Thêm lý do', parent::footer());
    }

    /**
     * Updates an existing ContactsReason model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

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
        ], 'Cập nhật lý do', parent::footer());
    }

    /**
     * Deletes an existing ContactsReason model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return self::responseSuccess();
    }

    /**
     * Finds the ContactsReason model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContactsReason the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactsReason::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
