<?php


namespace backend\controllers;


use backend\models\OrdersContact;
use backend\models\OrdersContactSearch;
use common\helper\Helper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii2tech\spreadsheet\Spreadsheet;

class AjaxExportController extends BaseController
{
    public function init()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init(); // TODO: Change the autogenerated stub
    }

    function actionOrder()
    {

        $searchModel = new OrdersContactSearch();
        $req = \Yii::$app->request->queryParams;
        $filter = ArrayHelper::getValue($req, 'OrdersContactSearch.filter', 'pending');
        $params = array_merge_recursive($req, [
            'OrdersContactSearch' => [
                'status' => $filter === OrdersContact::STATUS_PENDING ? [
                    OrdersContact::STATUS_PENDING
                ] : [
                    OrdersContact::STATUS_SHIPPED,
                    OrdersContact::STATUS_SHIPPING,
                    OrdersContact::STATUS_REFUND,
                    OrdersContact::STATUS_CANCEL,
                    OrdersContact::STATUS_UNCROSS,
                    OrdersContact::STATUS_CROSSED,
                    OrdersContact::STATUS_PAYED,
                    OrdersContact::STATUS_UNPAID
                ]
            ]
        ]);
        $dataProvider = $searchModel->search($params);

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'columns' => [
                'code',
                'name',
                'phone',
                'address',
                [
                    'attribute' => 'code',
                    'value' => function ($model) {
                        return $model->code;
                    }
                ],
                [
                    'attribute' => 'time_shipped_success',
                    'value' => function ($model) {
                        return Helper::dateFormat($model->time_shipped_success);
                    }
                ],
                [
                    'attribute' => 'warehouse_id',
                    'value' => function ($model) {
                        $warehouse = Helper::isEmpty($model->warehouse) ? null : $model->warehouse->name;
                        return $warehouse;
                    }
                ],
                [
                    'attribute' => 'transport_id',
                    'value' => function ($model) {
                        $transport = Helper::isEmpty($model->transporter) ? null : $model->transporter->name;
                        return $transport;
                    }
                ],
                [
                    'label' => 'Delivery count',
                    'value' => function ($model) {
                        return 1;
                    }
                ],
                [
                    'label' => 'Total Product',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Helper::printString($model, true);
                    }
                ],
                [
                    'label' => 'Số lượng SP',
                    'value' => function ($model) {
                        $qty = 0;
                        $items = Helper::isEmpty($model->skuItems) ? [] : $model->skuItems;
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                $qty += (int)$item->qty;
                            }
                        }
                        return $qty;
                    }
                ],
                [
                    'label' => 'Đối tác',
                    'value' => function ($model) {
                        $partner = !Helper::isEmpty($model->partner_name) ? $model->partner_name : null;
                        if (!$partner) {
                            $contact = !Helper::isEmpty($model->contact) ? $model->contact->partnerName : null;
                            if ($contact) {
                                return $contact->username;
                            }
                        }
                        return $partner;
                    }
                ],
                [
                    'label' => 'Item SKU',
                    'value' => function ($model) {
                        $sku = '';
                        $items = Helper::isEmpty($model->skuItems) ? [] : $model->skuItems;
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                $sku .= $item->sku;
                            }
                        }
                        return $sku;
                    }
                ],
                [
                    'label' => 'Item Quantity',
                    'value' => function ($model) {
                        $qty = '';
                        $items = Helper::isEmpty($model->skuItems) ? [] : $model->skuItems;
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                $qty .= $item->qty . ',';
                            }
                        }
                        return substr($qty, 0, -1);
                    }
                ],

                [
                    'label' => 'Kích thước (Volume)',
                    'value' => function ($model) {
                        $qty = '';
                        $items = Helper::isEmpty($model->skuItems) ? [] : $model->skuItems;
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                if (!$item->product) {
                                    continue;
                                }
                                $qty .= $item->product->size . ',';
                            }
                        }
                        return substr($qty, 0, -1);
                    }
                ],
                [
                    'label' => 'Khối lượng (Gram)',
                    'value' => function ($model) {
                        $qty = '';
                        $items = Helper::isEmpty($model->skuItems) ? [] : $model->skuItems;
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                if (!$item->product) {
                                    continue;
                                }
                                $qty .= $item->product->weight . ',';
                            }
                        }
                        return substr($qty, 0, -1);
                    }
                ],
                [
                    'label' => 'Phương thức thanh toán',
                    'value' => function ($model) {
                        $payment = Helper::isEmpty($model->payment) ? null : $model->payment->name;
                        return $payment;
                    }
                ],
                [
                    'label' => 'Link ảnh hóa đơn',
                    'value' => function ($model) {
                        return $model->bill_link;
                    }
                ],
                [
                    'label' => 'Doanh thu có ship (C8)',
                    'value' => function ($model) {
                        return Helper::toFloat($model->total_bill);
                    }
                ],
                'zipcode',
                [
                    'label' => 'Vùng',
                    'value' => function ($model) {
                        return Helper::countryName($model->country);
                    }
                ],

            ]
        ]);
        $file = UPLOAD_PATH . '/order_' . time() . '.xlsx';
        $exporter->save($file);
        return \Yii::$app->response->sendFile($file)->on(Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $file);
    }

    function actionCrossed()
    {
        $searchModel = new OrdersContactSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'OrdersContactSearch' => [
                'status' => [OrdersContact::STATUS_CROSSED],
                'cross_status' => [OrdersContact::STATUS_CROSSED],
            ]
        ]));


        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'columns' => [
                'code',
                'cross_check_code',
                'checking_number',
                'name',
                'phone',
                [
                    'label' => 'Đối tác',
                    'format' => 'html',
                    'value' => function ($model) {
                        if (Helper::isEmpty($model->contact)) {
                            return "---";
                        }
                        return $model->contact->partner;
                    }
                ],
                [
                    'label' => 'sản phẩm',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Helper::printString($model);
                    }
                ],
                [
                    'label' => 'Trạng thái',
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => function ($model) {
                        $status = OrdersContact::STATUS_PAYED;
                        if ($model->shipping_status == OrdersContact::STATUS_REFUND) {
                            $status = OrdersContact::STATUS_REFUND;
                        }
                        return OrdersContact::StatusLabel($status);
                    }
                ],
                [
                    'label' => 'Doanh thu',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Helper::formatCUR($model->total_bill, '', 0);
                    }
                ],
                [
                    'label' => 'Phí vận chuyển',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Helper::formatCUR($model->transport_fee, '', 2);
                    }
                ],
                [
                    'label' => 'Phí thu hộ',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Helper::formatCUR($model->collection_fee, '', 0);
                    }
                ],
                [
                    'label' => 'Phí dịch vụ',
                    'format' => 'html',
                    'value' => function ($model) {
                        # $service = Helper::isEmpty($model->contact->partner) ? 0 : $model->contact->partner->service_fee;
                        $service = $model->service_fee ? $model->$model : 0;
                        return Helper::formatCUR($service, '', 0);
                    }
                ],
                [
                    'label' => 'doanh thu đối soát',
                    'format' => 'html',
                    'value' => function ($model) {
                        #$service = Helper::isEmpty($model->contact->partner) ? 0 : $model->contact->partner->service_fee;
                        $service = $model->service_fee;
                        $amount = $model->total_bill - $model->collection_fee - $service;
                        return Helper::formatCUR($amount, '', 0);
                    }
                ]
            ]
        ]);
        $file = UPLOAD_PATH . '/order_crossed_' . time() . '.xlsx';
        $exporter->save($file);
        return \Yii::$app->response->sendFile($file)->on(Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $file);
    }
}
