<?php

use yii\db\Migration;

/**
 * Class m210203_203449_add_index_table_contact
 */
class m210203_203449_add_index_table_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_contacts', 'contacts', 'code,partner,country,register_time,type');
        $this->createIndex('idx_order_contacts', 'orders_contact', 'code,country,warehouse_id,order_time,sale');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_203449_add_index_table_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_203449_add_index_table_contact cannot be reverted.\n";

        return false;
    }
    */
}
