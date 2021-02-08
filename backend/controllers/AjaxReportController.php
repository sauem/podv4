<?php


namespace backend\controllers;


use backend\models\Contacts;
use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use backend\models\OrdersTopup;
use backend\models\OrdersTopupSearch;
use backend\models\UserRole;
use common\helper\Helper;
use Illuminate\Support\Arr;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AjaxReportController extends BaseController
{
    public function unRequiredAuthAction()
    {
        return [
            'sales'
        ];// TODO: Change the autogenerated stub
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionFinancialTopup()
    {
        $searchModel = new OrdersTopupSearch();
        $dataProvider = $searchModel->search([
            'OrdersTopupSearch' => \Yii::$app->request->queryParams
        ]);
        $dataProvider->query->with(['partner', 'medias']);
        $data = $dataProvider->query->asArray()->all();

        $total = ArrayHelper::getColumn($data, 'total');
        $counter['total'] = 0;
        $counter['total_ship'] = 0;
        $counter['total_ship_crossed'] = 0;
        $counter['total_remaining'] = 0;
        return [
            'index' => $counter,
            'data' => $data
        ];
    }

    /**
     * @param $query
     * @param $filter
     * @return mixed
     * @throws BadRequestHttpException
     */

    static function financialSearch(Query $query, $filter)
    {
        try {

            $product = ArrayHelper::getValue($filter, 'filter.product', []);
            $sale = ArrayHelper::getValue($filter, 'filter.sale', []);
            $marketer = ArrayHelper::getValue($filter, 'filter.marketer', []);
            $time_register = ArrayHelper::getValue($filter, 'filter.register_time', '');

            if (!empty($product)) {
                $query->innerJoin('products as P', 'P.partner_name = contacts.partner');
                $query->innerJoin('orders_contact_sku as I', 'P.sku = I.sku');
                $query->andWhere(['I.sku' => $product]);
                # Helper::printf($query->createCommand()->rawSql);
            }
            if (!Helper::isEmpty($sale)) {
                $query->innerJoin('contacts_assignment', 'contacts_assignment.phone = contacts.phone');
                $query->andWhere(['IN', 'contacts_assignment.user_id', $sale]);
            }
            if (!Helper::isEmpty($marketer)) {
                if (Helper::isEmpty($product)) {
                    $query->innerJoin('products as P', 'P.partner_name = contacts.partner')
                        ->andWhere('contacts.register_time >= P.marketer_rage_start AND contacts.register_time <= P.marketer_rage_end');
                } else {
                    $query->andWhere('contacts.register_time >= P.marketer_rage_start AND contacts.register_time <= P.marketer_rage_end');
                }
            }

            if (!Helper::isEmpty($time_register)) {
                $time_register = explode(' - ', $time_register);
                $startTime = Helper::timer(str_replace('/', '-', $time_register[0]));
                $endTime = Helper::timer(str_replace('/', '-', $time_register[1]), 1);
                $query->where(['between', 'contacts.register_time', $startTime, $endTime]);
            } else {
                $query->andWhere('FROM_UNIXTIME(contacts.register_time) >= (NOW() - INTERVAL 2 WEEK)');
                $query->andWhere('FROM_UNIXTIME(contacts.register_time) <= NOW()');
            }

        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return $query;
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionFinancial()
    {
        $isEmpty = false;
        $query = Contacts::find()
            ->leftJoin('orders_contact', 'orders_contact.code = contacts.code')
            ->addSelect([
                'SUM(orders_contact.total_bill) as revenue_C8',
                'SUM(IF(orders_contact.payment_status = "paid", orders_contact.total_bill ,0)) as revenue_C11',
                'SUM(IF(orders_contact.payment_status = "crossed", orders_contact.total_bill ,0)) as revenue_C13',
                'FROM_UNIXTIME(contacts.register_time, \'%d/%m/%Y\') day',
            ])->groupBy('day')
            ->orderBy('contacts.register_time ASC');
        if (Helper::isRole(UserRole::ROLE_PARTNER)) {
            $query->andWhere(['contacts.partner' => \Yii::$app->user->identity->username]);
        }

        if (\Yii::$app->request->isPost) {
            try {
                $query = static::financialSearch($query, \Yii::$app->request->post());
            } catch (\Exception $exception) {
                throw new BadRequestHttpException($exception->getMessage());
            }
        } else {
            $query->andWhere('FROM_UNIXTIME(contacts.register_time) >= (NOW() - INTERVAL 2 WEEK)');
            $query->andWhere('FROM_UNIXTIME(contacts.register_time) <= NOW()');
        }
        $result = $query->asArray()->all();
        if (!$result) {
            $isEmpty = true;
        }
        $result = array_map(function ($item) {
            return array_merge($item, [
                'C11_C8' => Helper::calculate($item['revenue_C11'], $item['revenue_C8']),
                'C13_C11' => Helper::calculate($item['revenue_C13'], $item['revenue_C11']),
            ]);
        }, $result);
        $labels = ArrayHelper::getColumn($result, 'day');
        $revenue_C8 = ArrayHelper::getColumn($result, 'revenue_C8');
        $revenue_C11 = ArrayHelper::getColumn($result, 'revenue_C11');
        $revenue_C13 = ArrayHelper::getColumn($result, 'revenue_C13');
        $C11_C8 = array_sum(ArrayHelper::getColumn($result, 'C11_C8'));
        $C13_C11 = array_sum(ArrayHelper::getColumn($result, 'C13_C11'));
        $total_revenue = array_sum(ArrayHelper::getColumn($result, 'revenue_C8'));

        return [
            'isEmpty' => $isEmpty,
            'labels' => $labels,
            'data' => [
                'C8' => $revenue_C8,
                'C11' => $revenue_C11,
                'C13' => $revenue_C13
            ],
            'counter' => [
                'C11_C8' => $C11_C8,
                'C13_C11' => $C13_C11,
                'total_revenue' => $total_revenue
            ]
        ];
    }

    /**
     * @param $query
     * @param $filter
     * @return mixed
     * @throws BadRequestHttpException
     */
    static function saleSearch($query, $filter)
    {
        try {

            $marketer = ArrayHelper::getValue($filter, 'filter.marketer', []);
            $product = ArrayHelper::getValue($filter, 'filter.product', []);
            $source = ArrayHelper::getValue($filter, 'filter.contact_source', []);
            $sale = ArrayHelper::getValue($filter, 'filter.sale', []);

            $time_register = ArrayHelper::getValue($filter, 'filter.register_time', '');

            if (!Helper::isEmpty($time_register)) {
                $time_register = explode(' - ', $time_register);
                $startTime = Helper::timer(str_replace('/', '-', $time_register[0]));
                $endTime = Helper::timer(str_replace('/', '-', $time_register[1]), 1);
                $query->where(['between', 'contacts.register_time', $startTime, $endTime]);
            } else {
                $query->andWhere('FROM_UNIXTIME(contacts.register_time) >= (NOW() - INTERVAL 2 WEEK)');
                $query->andWhere('FROM_UNIXTIME(contacts.register_time) <= NOW()');
            }
            if (!empty($source)) {
                $query->andWhere(['IN', 'contacts.type', $source]);
            }

            if (!Helper::isEmpty($product)) {
                $query->innerJoin('products as P', 'P.partner_name = contacts.partner');
                $query->innerJoin('orders_contact_sku as I', 'P.sku = I.sku');
                $query->andWhere(['I.sku' => $product]);
                # Helper::printf($query->createCommand()->rawSql);
            }
            if (!Helper::isEmpty($sale)) {
                $query->innerJoin('contacts_assignment', 'contacts_assignment.phone = contacts.phone');
                $query->andWhere(['IN', 'contacts_assignment.user_id', $sale]);
            }
            if (!Helper::isEmpty($marketer)) {
                if (Helper::isEmpty($product)) {
                    $query->innerJoin('products as P', 'P.partner_name = contacts.partner')
                        ->andWhere('contacts.register_time >= P.marketer_rage_start AND contacts.register_time <= P.marketer_rage_end');
                } else {
                    $query->andWhere('contacts.register_time >= P.marketer_rage_start AND contacts.register_time <= P.marketer_rage_end');
                }
            }

        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return $query;
    }


    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSales()
    {
        $isEmpty = false;
        $query = Contacts::find()
            ->leftJoin('orders_contact', 'orders_contact.code = contacts.code')
            ->addSelect([
                'contacts.*',
                'orders_contact.*',
                'SUM(IF( contacts.status != "duplicate", 1, 0)) as C3',
                'SUM(IF( contacts.status = "ok", 1, 0 )) as C8',
                'SUM(IF( contacts.status = "cancel", 1, 0 )) as C6',
                'SUM(IF( contacts.status = "callback" OR contacts.status = "pending", 1, 0 )) as C7',
                'SUM(IF( contacts.status = "number_fail", 1, 0 )) as C4',
                'SUM(IF( contacts.status = "new", 1, 0 )) as C0',
                'SUM(orders_contact.total_bill) as revenueC8',
                'SUM(IF(orders_contact.payment_status = "paid",1,0)) as C11',
                'FROM_UNIXTIME(contacts.register_time, \'%d/%m/%Y\') day',
            ])->groupBy('day')->orderBy('contacts.register_time ASC');

        $error = "";
        if (Helper::isRole(UserRole::ROLE_PARTNER)) {
            $query->where(['contacts.partner' => \Yii::$app->user->identity->username]);
        }

        if (\Yii::$app->request->isPost) {
            try {
                $query = static::saleSearch($query, \Yii::$app->request->post());
            } catch (BadRequestHttpException $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        } else {
            $query->andWhere('FROM_UNIXTIME(contacts.register_time) >= (NOW() - INTERVAL 2 WEEK)');
            $query->andWhere('FROM_UNIXTIME(contacts.register_time) <= NOW()');
        }

        $result = $query->asArray()->all();
        if (empty($result)) {
            $isEmpty = true;
        }
        $result = array_map(function ($item) {
            return array_merge($item, [
                'C8_C3' => Helper::calculate($item['C8'], $item['C3'])
            ]);
        }, $result);
        $labels = ArrayHelper::getColumn($result, 'day');
        $C8 = ArrayHelper::getColumn($result, 'C8');
        $C3 = ArrayHelper::getColumn($result, 'C3');
        $C4 = ArrayHelper::getColumn($result, 'C4');
        $C6 = ArrayHelper::getColumn($result, 'C6');
        $C7 = ArrayHelper::getColumn($result, 'C7');
        $C0 = ArrayHelper::getColumn($result, 'C0');
        $C11 = ArrayHelper::getColumn($result, 'C11');
        $C8_C3 = ArrayHelper::getColumn($result, 'C8_C3');
        $revenue_C8 = ArrayHelper::getColumn($result, 'revenueC8');
        $revenue_C8 = array_sum($revenue_C8);
        $totalC3 = array_sum($C3);
        $totalC8 = array_sum($C8);
        $totalC8_C3 = Helper::calculate($totalC8, $totalC3);
        $totalC11 = array_sum($C11);
        $totalC11_C3 = Helper::calculate($totalC11, $totalC3);
        $totalC11_C8 = Helper::calculate($totalC11, $totalC8);

        return [
            'isEmpty' => $isEmpty,
            'labels' => $labels,
            'data' => [
                'C8' => $C8,
                'C3' => $C3,
                'C8_C3' => $C8_C3,
                'C4' => $C4,
                'C6' => $C6,
                'C7' => $C7,
                'C0' => $C0
            ],
            'counter' => [
                'revenue_C8' => $revenue_C8,
                'totalC8' => $totalC8,
                'totalC3' => $totalC3,
                'totalC8_C3' => $totalC8_C3,
                'totalC11' => $totalC11,
                'totalC11_C8' => $totalC11_C8,
                'totalC11_C3' => $totalC11_C3
            ],
            'error' => $error
        ];
    }

}
