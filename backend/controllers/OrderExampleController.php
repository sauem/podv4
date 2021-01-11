<?php


namespace backend\controllers;


use backend\models\OrdersExample;
use backend\models\OrdersExampleItem;
use backend\models\OrdersExampleSearch;
use common\helper\Helper;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class OrderExampleController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new OrdersExampleSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new OrdersExample();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                OrdersExampleItem::saveItems($model->id, \Yii::$app->request->post('items'));
                $transaction->commit();
                return static::responseSuccess(1, 1, 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('warning', $exception->getMessage());
            }

        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Tạo đơn mẫu', $this->footer(), 'md');
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = OrdersExample::findOne($id);
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang!');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                OrdersExampleItem::saveItems(\Yii::$app->request->post('items'));
                $transaction->commit();
                return static::responseSuccess(1, 1, 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('warning', $exception->getMessage());
            }
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
        ], 'Cập nhật đơn hàng mẫu', $this->footer());
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
        $model = OrdersExample::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang!');
        }
        $model->delete();
        return static::responseSuccess();
    }

}