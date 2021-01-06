<?php

use yii\db\Migration;

/**
 * Class m210106_081958_add_column_bill_link
 */
class m210106_081958_add_column_bill_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("orders_contact", "bill_link", $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210106_081958_add_column_bill_link cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210106_081958_add_column_bill_link cannot be reverted.\n";

        return false;
    }
    */
}
