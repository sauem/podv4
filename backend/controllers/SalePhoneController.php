<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsAssignment;
use backend\models\ContactsSearch;
use backend\models\OrdersContact;
use backend\models\OrdersContactSku;
use backend\models\Products;
use common\helper\Helper;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class SalePhoneController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new ContactsSearch();
        $assignPhone = ContactsAssignment::getPhoneAssign();
        if (!$assignPhone) {
            \Yii::$app->session->setFlash('warning', 'Hiện tại bạn chưa có số điện thoại nào!');
        }
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [Contacts::STATUS_NEW],
                'phone' => $assignPhone
            ]
        ]));
        $contactOK = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [Contacts::STATUS_OK],
                'phone' => $assignPhone
            ]
        ]));
        $contactFail = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [
                    Contacts::STATUS_CANCEL,
                    Contacts::STATUS_DUPLICATE,
                    Contacts::STATUS_NUMBER_FAIL
                ],
                'phone' => $assignPhone
            ]
        ]));
        $contactCallback = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [
                    Contacts::STATUS_PENDING,
                    Contacts::STATUS_CALLBACK
                ],
                'phone' => $assignPhone
            ]
        ]));
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
            'assignPhone' => $assignPhone,
            'contactOK' => $contactOK,
            'contactCallback' => $contactCallback,
            'contactFail' => $contactFail
        ]);
    }

    /**
     * @param $code
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreate($code)
    {


        $contact = Contacts::findOne(['code' => $code]);
        $model = new OrdersContact();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);

        $data = ArrayHelper::toArray($contact);
        unset($data['id']);

        $model->load($data, '');
        $products = Products::find()->asArray()->all();
        if (!$contact) {
            throw new BadRequestHttpException('Contact not founded!');
        }

        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                if ($model->save()) {
                    Contacts::updateStatus($code, Contacts::STATUS_OK);
                    OrdersContactSku::saveItems($model->id, \Yii::$app->request->post('items'));
                    $transaction->commit();
                    return static::responseSuccess();
                }
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('warning', $exception->getMessage());
            }
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
            'contact' => $contact,
            'products' => $products,
        ], '<i class="fe-shopping-cart"></i> Tạo đơn hàng', $this->footer(), 'xl');
    }
}