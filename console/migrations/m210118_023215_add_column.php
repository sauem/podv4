<?php

use yii\db\Migration;

/**
 * Class m210118_023215_add_column
 */
class m210118_023215_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("orders_contact", "service_fee", $this->double(15.2)->defaultValue(0));
        $this->addColumn("orders_contact", "sale", $this->integer()->null());
        $this->addColumn("payments", "slug", $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210118_023215_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_023215_add_column cannot be reverted.\n";

        return false;
    }
    */
}
