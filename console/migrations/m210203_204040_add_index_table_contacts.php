<?php

use yii\db\Migration;

/**
 * Class m210203_204040_add_index_table_contacts
 */
class m210203_204040_add_index_table_contacts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('idx_contacts', 'contacts');
        $this->dropIndex('idx_order_contacts', 'orders_contact');

        $this->createIndex('idx_contacts', 'contacts', [
            'code', 'partner', 'country', 'register_time', 'type'
        ]);
        $this->createIndex('idx_order_contacts', 'orders_contact',
            [
                'code', 'country', 'warehouse_id', 'order_time', 'sale'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_204040_add_index_table_contacts cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_204040_add_index_table_contacts cannot be reverted.\n";

        return false;
    }
    */
}
