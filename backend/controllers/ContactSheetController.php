<?php


namespace backend\controllers;


use backend\models\ContactsSheet;
use backend\models\ContactsSheetSearch;
use common\helper\Helper;

class ContactSheetController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new ContactsSheetSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render("index.blade", [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new ContactsSheet();
        $model->country = \Yii::$app->cache->get('country');
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess(1, 1, "Thao tác thành công!");
            }
            \Yii::$app->session->setFlash("danger", Helper::firstError($model));
        }
        return self::responseRemote("create.blade", [
            'model' => $model,
        ], "Tạo sheet dữ liệu đối tác", $this->footer());
    }


    public function actionUpdate($id)
    {
        $model = ContactsSheet::findOne($id);

        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                return static::responseSuccess(1, 1, "Thao tác thành công!");
            }
            \Yii::$app->session->setFlash("danger", Helper::firstError($model));
        }
        return self::responseRemote("create.blade", [
            'model' => $model,
        ], "Cập nhật sheet dữ liệu đối tác", $this->footer());
    }

    public function actionDelete($id)
    {
        $model = ContactsSheet::findOne($id);
        if (!$model) {
            return static::responseSuccess(0, 0, 'Not found!');
        }
        $model->delete();
        return self::responseSuccess();
    }

}
