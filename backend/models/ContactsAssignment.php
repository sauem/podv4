<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use common\models\BaseModel;
use yii\db\Expression;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "contacts_assignment".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $phone
 * @property string|null $status
 * @property string|null $country
 * @property int $created_at
 * @property int $updated_at
 */
class ContactsAssignment extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';

    public static function tableName()
    {
        return 'contacts_assignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'phone'], 'required'],
            [['phone', 'status', 'country'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'status' => 'Status',
            'country' => 'Country',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(UserModel::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function getPhoneAssign()
    {
        $user = Yii::$app->user->getId();
        $model = ContactsAssignment::findOne(['user_id' => $user, 'status' => ContactsAssignment::STATUS_PROCESSING]);
        if (!$model) {
            $model = self::nextAssignment();
            if (!$model) {
                return false;
            }
        }
        return $model->phone;
    }

    public static function nextAssignment()
    {
        $model = ContactsAssignment::find()
            ->where(['user_id' => Yii::$app->user->getId()])
            ->andFilterWhere(['<>', 'status', ContactsAssignment::STATUS_COMPLETED])->one();
        if (!$model) {
            return false;
        }
        $model->status = ContactsAssignment::STATUS_PROCESSING;
        if (!$model->save()) {
            return false;
        }
        return $model;
    }

    public static function completeAssignment($phone)
    {
        $phoneAssign = ContactsAssignment::findOne(['phone' => $phone, 'user_id' => Yii::$app->user->getId()]);

        if ($phoneAssign && static::hasComplete($phone)) {
            $phoneAssign->status = ContactsAssignment::STATUS_COMPLETED;
            self::nextAssignment();
            return $phoneAssign->save();
        }
        return false;
    }

    public static function getNewPhone()
    {
        $contact = Contacts::find()
            ->leftJoin('contacts_assignment', 'contacts_assignment.phone = contacts.phone')
            ->where('contacts_assignment.phone IS NULL')
            ->andWhere(['contacts.country' => Yii::$app->cache->get('country')]);
        if (Helper::compareTimeNow()) {
            $contact->andWhere([
                'contacts.status' => Contacts::STATUS_NEW
            ]);
        } else {
            $contact->andWhere([
                'contacts.status' => [Contacts::STATUS_CALLBACK, Contacts::STATUS_NEW],
            ])->orderBy(new Expression('FIELD(contacts.status, "callback, new")'));
        }
        $contact = $contact->groupBy('phone')->one();

        if ($contact) {
            $model = new ContactsAssignment();
            $model->user_id = Yii::$app->user->getId();
            $model->status = ContactsAssignment::STATUS_PROCESSING;
            $model->phone = $contact->phone;
            if ($model->save()) {
                return $model->phone;
            }
        }
        return false;
    }

    public static function hasComplete($phone)
    {
        $contact = Contacts::findAll(['phone' => $phone, 'status' => Contacts::STATUS_NEW]);
        if (count($contact) > 0) {
            return false;
        }
        return true;
    }

    public static function getPhoneCallDone()
    {
        $user = Yii::$app->user->getId();
        $count = ContactsAssignment::find()->where(['user_id' => $user])
            ->andWhere(['contacts_assignment.status' => ContactsAssignment::STATUS_COMPLETED])
            ->andWhere('FROM_UNIXTIME(contacts_assignment.created_at) >= NOW() - INTERVAL 1 DAY')
            ->count();
        if (!$count) {
            return 0;
        }
        return $count;
    }
}
