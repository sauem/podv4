<?php


namespace backend\controllers;


use backend\models\ZipcodeCountry;
use common\helper\Helper;
use cyneek\yii2\blade\BladeBehavior;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function init()
    {
        $countries = ZipcodeCountry::find()->addSelect(['name', 'code', 'symbol'])->groupBy('code')->all();
        $countriesParams = ArrayHelper::map($countries, 'code', function ($item) {
            return $item->code . '-' . $item->name;
        });
        $symbols = ArrayHelper::map($countries, 'code', 'symbol');
        \Yii::$app->params['countries'] = $countriesParams;
        \Yii::$app->params['symbols'] = $symbols;
        \Yii::$app->language = \Yii::$app->cache->get('language');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => array_merge(['error'], $this->unRequiredAuthAction()),
                        'allow' => true,
                    ],
                    [
                        'actions' => $this->requiredAuthAction(),
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    []
                ],
            ],
            'blade' => [
                'class' => BladeBehavior::class
            ],
        ];
    }


    public function unRequiredAuthAction()
    {
        return [];
    }

    public function requiredAuthAction()
    {
        return [];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render(
                '@backend/views/site/error.blade',
                [
                    'exception' => $exception
                ]
            );
        }
    }

    static function responseClose($message = '')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'forceReload' => 1,
            'message' => $message
        ];

    }

    static function responseSuccess($forceReload = 1, $forceClose = 1, $message = '', $type = 'success')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'forceReload' => $forceReload,
            'forceClose' => $forceClose,
            'message' => $message,
            'type' => $type
        ];
    }

    static function responseSearch($context, $view, $params = [])
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'data' => $params,
                'content' => $context->renderAjax("@backend/views/" . \Yii::$app->controller->id . "/$view", $params)
            ];
        } else {
            return $context->render("@backend/views/" . \Yii::$app->controller->id . "/$view", $params);
        }
    }

    static function responseRemote($view, $params = [], $title = null, $footer = null, $size = 'normal')
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->layout = 'blank.blade';
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'content' => \Yii::$app->view->renderAjax('@backend/views/' . \Yii::$app->controller->id . '/' . $view, $params),
                'footer' => $footer,
                'status' => 1,
                'title' => $title,
                'size' => $size
            ];
        }
        return \Yii::$app->view->render('@backend/views/' . \Yii::$app->controller->id . '/' . $view, $params);
    }

    public function footer($note = null)
    {
        if (!$note) {
            $note = '* ' . \Yii::t('app', 'required_note');
        }
        return '<div class="d-flex w-100 justify-content-between"><button class="btn btn-secondary" data-dismiss="modal" >' . \Yii::t('app', 'close') . '</button><div><span class="mr-3 text-warning">' . $note . '</span><button class="btn btn-success" type="submit">' . \Yii::t('app', 'save') . '</button></div></div>';
    }
}