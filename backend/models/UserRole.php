<?php


namespace backend\models;


use common\helper\Helper;
use mdm\admin\models\Assignment;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class UserRole extends UserModel
{
    const ROLE_ADMIN = 'Admin';
    const ROLE_SALE = 'Sale';
    const ROLE_MARKETER = 'Marketer';
    const ROLE_VENDOR = 'Vendor';
    const ROLE_PARTNER = 'Partner';
    const ROLE_CS = 'Customer Care';

    const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_MARKETER => 'Marketer',
        self::ROLE_SALE => 'Sale',
        self::ROLE_VENDOR => 'Vận đơn',
        self::ROLE_PARTNER => 'Đối tác',
        self::ROLE_CS => 'Chăm sóc khách hàng',
    ];

    public static function assignRole($model)
    {
        $auth = new Assignment($model->id, $model);
        return $auth->assign([
            $model->role
        ]);
    }

    public static function ROLES()
    {
        $roles = AuthItem::find()->where(['type' => '1'])->asArray()->all();
        return ArrayHelper::map($roles, 'name', 'name');
    }

    public static function LISTS($role = UserRole::ROLE_PARTNER, $byCountry = false)
    {
        $query = UserModel::find()->joinWith([
            'position' => function ($query) use ($role) {
                $query->andWhere(['{{auth_assignment}}.item_name' => $role]);
            }
        ], false);
        if ($byCountry) {
            $country = \Yii::$app->cache->get('country');
            $query->andWhere(['{{user}}.country' => $country]);
        }
        $query = $query->all();
        return ArrayHelper::map($query, 'id', 'username');
    }

    /**
     * @param $permissions
     * @throws BadRequestHttpException
     */
    public static function assignPermission($model, $permissions)
    {
        try {
            if (Helper::isEmpty($permissions)) {
                throw new BadRequestHttpException("Chưa trọn quyền quản trị!");
            }
            $auth = new Assignment($model->id, $model);
            return $auth->assign($permissions);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public static function revokePermission($userId)
    {
        try {
            return AuthAssignment::deleteAll([
                'user_id' => $userId
            ]);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
