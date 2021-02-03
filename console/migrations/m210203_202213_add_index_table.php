<?php

use yii\db\Migration;

/**
 * Class m210203_202213_add_index_table
 */
class m210203_202213_add_index_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_p_sku', 'products', 'sku');
        $this->createIndex('idx_order_sku', 'orders_contact_sku', 'sku');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_202213_add_index_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_202213_add_index_table cannot be reverted.\n";

        return false;
    }
    */
}
