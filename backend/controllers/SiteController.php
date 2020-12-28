<?php

namespace backend\controllers;

use backend\models\Backups;
use backend\models\ContactsSource;
use backend\models\Products;
use backend\models\UserRole;
use common\helper\Helper;
use common\helper\SheetApi;
use common\models\Common;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii2mod\settings\actions\SettingsAction;

/**
 * Site controller
 */
class SiteController extends BaseController
{

    public function actions()
    {
        return array_merge(parent::actions(), [
            'settings' => [
                'class' => SettingsAction::class,
                'viewParams' => [],
                'view' => 'settings.blade',
                'on beforeSave' => function ($event) {

                    foreach ($event->form->attributes as $key => $attribute) {
                        if (empty($attribute)) {
                            Yii::$app->settings->remove("Common", $key);
                        }
                    }
                },
                'on afterSave' => function ($event) {

                },
                'modelClass' => Common::class,
            ],
        ]);
    }

    public function requiredAuthAction()
    {
        return [
            'index', 'logout', 'settings','report'
        ]; // TODO: Change the autogenerated stub
    }

    public function unRequiredAuthAction()
    {
        return [
            'login'
        ]; // TODO: Change the autogenerated stub
    }

    public function actionIndex()
    {
        $sources = ContactsSource::LISTS();
        $products = Products::LISTS();
        $marketers = UserRole::LISTS(UserRole::ROLE_MARKETER);


        return $this->render('index.blade', [
            'sources' => $sources,
            'products' => $products,
            'marketers' => $marketers
        ]);
    }
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'auth.blade';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login.blade', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


}
