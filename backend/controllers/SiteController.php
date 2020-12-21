<?php

namespace backend\controllers;

use backend\models\ContactsSource;
use backend\models\Products;
use backend\models\UserRole;
use common\helper\Helper;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends BaseController
{

    public function requiredAuthAction()
    {
        return [
            'index', 'logout'
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
