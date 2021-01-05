<?php


namespace common\helper;

use backend\models\Contacts;
use backend\models\OrdersContact;
use common\helper\Helper;
use GuzzleHttp\Client;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;


class SheetApi
{
    public $client;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setClientId(GOOGLE_DRIVE_CLIENT_ID);
        $this->client->setClientSecret(GOOGLE_DRIVE_CLIENT_SECRET);
        $this->client->refreshToken(GOOGLE_DRIVE_REFRESH_TOKEN);
        $this->client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
        $this->client->setAccessType('offline');
//        $this->client->setHttpClient(new Client([
//            'verify' => "D:\cacert.pem"
//        ]));

    }

    /**
     * @throws BadRequestHttpException
     */
    public function pushOrder()
    {
        $sheet = new \Google_Service_Sheets($this->client);
        $spreadsheetId = Helper::setting('sheet_id');
        $result = false;
        try {
            $orders = OrdersContact::find()
                ->with(['skuItems', 'warehouse', 'payment', 'transporter', 'contact'])
                ->all();
            if (!$orders) {
                throw new BadRequestHttpException('No record found!');
            }
            $dataCore = array_map(function ($order) {
                return array_values([
                    'code' => $order->code,
                    'register_time' => Helper::isEmpty($order->contact) ? "" : Helper::toDate($order->contact->register_time),
                    'name' => $order->name,
                    'phone' => $order->phone,
                    'address' => $order->address,
                     'category' => "",
                    'zipcode' => $order->zipcode,
                    'city' => $order->city,
                    'page' => "",
                    // Nguồn contact
                    'type' => $order->order_source,
                    // Đối tác
                    'partner' => Helper::isEmpty($order->contact) ? "" : $order->contact->partner,
                    //Marketer quản lý
                    'marketer' => "",
                    //Sale quản lý
                    'sale' => "",
                    // Link facebook
                    'social_link' => "",
                    // phương thức thanh toán
                    'payment' => Helper::isEmpty($order->payment) ? "" : $order->payment->name,
                    //Link ảnh hóa đơn chuyển tiền
                    'bill_media' => "",
                    //Doanh thu C8
                    'revenue_C8' => "0",
                    // Tình trạng chốt đơn
                    'contact_status' => ArrayHelper::getValue(Contacts::STATUS, $order->contact->status, ""),
                    //Tình trạng thanh toán
                    'payment_status' => Helper::isNull($order->payment_status),
                    //Tình trạng vận chuyển
                    'status' => ArrayHelper::getValue(OrdersContact::STATUS, $order->status, ""),
                    //Phí vận chuyển
                    'transport_fee' => Helper::isNull($order->transport_fee),
                    //Phí thu hộ
                    'collection_fee' => Helper::isNull($order->collection_fee),
                    // Tình trạng chuyển tiền C13
                    'cross_status' => ArrayHelper::getValue(OrdersContact::STATUS, $order->cross_status, ""),
                    // Ngày chuyển tiền C13
                    'remittance_date' => Helper::isNull(Helper::toDate($order->remittance_date)),
                ]);
            }, $orders);
           # Helper::printf($dataCore);
            $body = new \Google_Service_Sheets_ValueRange([
                'values' => $dataCore
            ]);

            $result = $sheet->spreadsheets_values->update($spreadsheetId, 'A2:AA',
                $body, ['valueInputOption' => 'RAW']);
            if (\Yii::$app instanceof \Yii\console\Application) {
                return "Done";
            }

        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return $result;
    }

}