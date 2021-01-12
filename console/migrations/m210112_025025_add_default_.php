<?php

use yii\db\Migration;

/**
 * Class m210112_025025_add_default_
 */
class m210112_025025_add_default_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payments', [
            'id' => 9999,
            'name' => 'Chuyển khoản',
            'description' => 'Thanh toán hình thức chuyển khoản',
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_025025_add_default_ cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210112_025025_add_default_ cannot be reverted.\n";

        return false;
    }
    */
}
