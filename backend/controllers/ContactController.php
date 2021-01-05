<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsSearch;
use backend\models\ContactsSource;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ContactController extends BaseController
{
    public function actionIndex()
    {
        $model = new Contacts();
        $searchModel = new ContactsSearch();

        $waitingContact = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'contacts.status' => [Contacts::STATUS_NEW],
            ],
        ]));
        $waitingContact->query->leftJoin('contacts_assignment', 'contacts_assignment.phone = contacts.phone')
            ->where('contacts_assignment.phone IS NULL');
        $allContact = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index.blade', [
            'model' => $model,
            'searchModel' => $searchModel,
            'allContact' => $allContact,
            'waitingContact' => $waitingContact
        ]);
    }

    public function actionSource()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ContactsSource::find()
        ]);
        return $this->render('source.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreateSource()
    {
        $model = new ContactsSource();
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess();
            }
            \Yii::$app->session->setFlash('danger', Helper::firstError($model));
        }
        return static::responseRemote('create-source.blade', [
            'model' => $model
        ], 'Thêm nguồn liên hệ', $this->footer());
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {

        $model = ContactsSource::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang!');
        }
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess();
            }
            \Yii::$app->session->setFlash('danger', Helper::firstError($model));
        }
        return static::responseRemote('create-source.blade', [
            'model' => $model
        ], 'Sửa nguồn liên hệ', $this->footer());
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = ContactsSource::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }
}