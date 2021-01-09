<?php

namespace console\controllers;

use backend\models\Contacts;
use backend\models\ContactsSheet;
use common\helper\Helper;
use common\helper\SheetApi;
use yii\web\BadRequestHttpException;

class AutoController extends \yii\console\Controller
{
    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionIndex()
    {
        $api = new SheetApi();
        $model = ContactsSheet::find()->all();
        if (!$model) {
            throw new BadRequestHttpException(Helper::firstError($model));
        }

        foreach ($model as $partner) {
            $sheetId = $partner->sheet_id;

            $sheet = new \Google_Service_Sheets($api->client);
            $range = "{$partner->category->name}!A1:I";
            echo $range . "\n";
            $response = $sheet->spreadsheets_values->get($sheetId, $range);
            $values = $response->getValues();
            unset($values[0]);
            if (empty($values)) {
                continue;
            };

            foreach ($values as $item) {
                try {
                    $model = new Contacts();
                    $data = [
                        'register_time' => Helper::timer(str_replace("/", "-", $item[0])),
                        'name' => $item[1],
                        'phone' => $item[2],
                        'address' => isset($item[3]) ? $item[3] : null,
                        'zipcode' => isset($item[4]) ? $item[4] : null,
                        'option' => isset($item[5]) ? $item[5] : null,
                        'note' => isset($item[6]) ? $item[6] : null,
                        'country' => $partner->country,
                        'category' => $partner->category->name,
                        'partner' => $partner->partner->username,
                        'type' => isset($item[8]) ? $item[8] : null,
                    ];
                    if ($partner->contact_source === ContactsSheet::SOURCE_REQUIRED && Helper::isEmpty($data['type'])) {
                        printf("Nguồn liên hệ bắt buộc");
                    } else {
                        $model->load($data, "");
                        if (!$model->save()) {
                            printf("success\n");
                        }
                    }
                } catch (\Exception $exception) {
                    printf($exception->getMessage() . "\n");
                }
            }
        }
        echo "done";
    }

    public function actionTest()
    {
        echo "HELLO";
    }
}