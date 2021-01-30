<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\ContactsAssignment;
use backend\models\ContactsLogStatus;
use backend\models\ContactsSearch;
use backend\models\OrdersContact;
use backend\models\OrdersContactSku;
use backend\models\OrderStatus;
use backend\models\Products;
use backend\models\WarehouseHistories;
use backend\models\WarehouseTransaction;
use backend\models\ZipcodeCountry;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class SalePhoneController extends BaseController
{
    public $assignPhone;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $assignPhone = ContactsAssignment::getPhoneAssign();
        if (!$assignPhone) {
            $assignPhone = ContactsAssignment::getNewPhone();
            if (!$assignPhone) {
                \Yii::$app->session->setFlash('warning', 'Hiện tại bạn chưa có số điện thoại nào!');
            }
        }
        $this->assignPhone = $assignPhone;
    }

    public function actionIndex()
    {
        ContactsAssignment::completeAssignment($this->assignPhone);
        ContactsAssignment::nextAssignment();

        $searchModel = new ContactsSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [Contacts::STATUS_NEW],
                'phone' => $this->assignPhone
            ]
        ]));


        return $this->render('index.blade', [
            'dataProvider' => $dataProvider,
            'assignPhone' => $this->assignPhone,

        ]);
    }

    public function actionHistories()
    {
        $contactHistories = new ActiveDataProvider([
            'query' => ContactsLogStatus::find()->orderBy('created_at DESC'),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render('histories.blade', [
            'dataProvider' => $contactHistories
        ]);
    }

    public function actionPending()
    {
        $searchModel = new ContactsSearch();

        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [
                    Contacts::STATUS_PENDING,
                ],
            ]
        ]));
        $dataProvider->query->innerJoin('contacts_assignment', 'contacts.phone = contacts_assignment.phone')
            ->andFilterWhere(['contacts_assignment.user_id' => \Yii::$app->user->getId()]);
        return static::responseRemote('tabs/holdup.blade', [
            'dataProvider' => $dataProvider,
            'id' => 'pending',
            'flash' => false
        ]);
    }

    public function actionCallback()
    {
        $searchModel = new ContactsSearch();

        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [
                    // Contacts::STATUS_PENDING,
                    Contacts::STATUS_CALLBACK
                ],
                #'phone' => $this->assignPhone
            ]
        ]));
        $dataProvider->query->innerJoin('contacts_assignment', 'contacts.phone = contacts_assignment.phone')
            ->andFilterWhere(['contacts_assignment.user_id' => \Yii::$app->user->getId()]);
        return static::responseRemote('tabs/holdup.blade', [
            'dataProvider' => $dataProvider,
            'id' => 'callback',
            'flash' => false
        ]);
    }

    public function actionFail()
    {
        $searchModel = new ContactsSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [
                    Contacts::STATUS_CANCEL,
                    Contacts::STATUS_DUPLICATE,
                    Contacts::STATUS_NUMBER_FAIL
                ],
                #'phone' => $this->assignPhone
            ]
        ]));
        $dataProvider->query->innerJoin('contacts_assignment', 'contacts.phone = contacts_assignment.phone')
            ->andFilterWhere(['contacts_assignment.user_id' => \Yii::$app->user->getId()]);
        return static::responseRemote('tabs/holdup.blade', [
            'dataProvider' => $dataProvider,
            'id' => 'fail',
            'flash' => false
        ]);
    }

    public function actionChangeStatus()
    {
        $ids = \Yii::$app->request->get('ids');
        $status = \Yii::$app->request->get('status');
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        if (\Yii::$app->request->isPost) {
            try {
                $note = \Yii::$app->request->post('sale_note');
                $note = Helper::isEmpty($note) ? null : $note;

                Contacts::updateAll(['status' => $status], ['id' => $ids]);
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        $contact = Contacts::findOne($id);
                        if (!$contact) {
                            throw new BadRequestHttpException("Không tìm thấy liên hệ!");
                        }
                        ContactsLogStatus::saveRecord($contact->code, $contact->phone, $status, $note);
                    }
                } else {
                    $contact = Contacts::findOne($ids);
                    if (!$contact) {
                        throw new BadRequestHttpException("Không tìm thấy liên hệ!");
                    }
                    ContactsLogStatus::saveRecord($contact->code, $contact->phone, $status, $note);
                }
                ContactsAssignment::completeAssignment(ContactsAssignment::getPhoneAssign());
                $new = ContactsAssignment::nextAssignment();
                $transaction->commit();
                return static::responseSuccess(1, 1, $new ? 'Số mới được áp dung!' : 'Thao tác thành công');
            } catch
            (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('warning', $exception->getMessage());
            }
        }
        return static::responseRemote('change-status.blade', [], 'Đổi trạng thái', $this->footer());
    }

    public function actionSuccess()
    {
        $searchModel = new ContactsSearch();

        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'ContactsSearch' => [
                'status' => [Contacts::STATUS_OK],
                #'phone' => $this->assignPhone
            ]
        ]));
        $dataProvider->query->innerJoin('contacts_assignment', 'contacts.phone = contacts_assignment.phone')
            ->andFilterWhere(['contacts_assignment.user_id' => \Yii::$app->user->getId()]);
        return static::responseRemote('tabs/success.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param $code
     * @return array|string
     * @throws BadRequestHttpException
     */
    public
    function actionCreate($code)
    {
        $model = new OrdersContact();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        $products = Products::LISTS();
        $model->country = \Yii::$app->cache->get('country');

        if ($code !== 'new') {
            $contact = Contacts::findOne(['code' => $code]);
            if (!$contact) {
                throw new BadRequestHttpException('Contact not founded!');
            }
            $data = ArrayHelper::toArray($contact);
            unset($data['id']);
            $model->load($data, '');
            $country = ZipcodeCountry::findOne(['zipcode' => $contact->zipcode]);
            if($country){
                $model->city = $country->city;
                $model->district = $country->district;
                $model->country = $contact->country;
            }
        }
        $model->items = \Yii::$app->request->post('items');
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            try {
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
                //Update new info contact
                Contacts::updateStatus($code, $model);
                // Save contact call log
                OrdersContactSku::saveItems($model->id, \Yii::$app->request->post('items'));
                //save item order product
                ContactsLogStatus::saveRecord($model->code, $model->phone, Contacts::STATUS_OK);
                //Update warehouse
                WarehouseHistories::saveHistories($model->code, \Yii::$app->request->post('items'));
                //check user has finish current phone
                $newNumber = ContactsAssignment::completeAssignment($model->phone);
                $transaction->commit();

                return static::responseSuccess(1, 1, $newNumber ? "Số mới được áp dụng!" : 'Thao tác thành công!');
            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('warning', $exception->getMessage());
                if ($code == 'new') {
                    return static::responseSuccess(0, 0, $exception->getMessage());
                }
            }
        }
        return static::responseRemote('create.blade', [
            'model' => $model,
            'contact' => isset($contact) ? $contact : null,
            'products' => $products,
        ], '<i class="fe-shopping-cart"></i> Tạo đơn hàng', $this->footer(), 'xl');
    }
}
