<?php

use yii\db\Migration;

/**
 * Class m210203_204733_add_index_table_contact
 */
class m210203_204733_add_index_table_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_p_partner', 'products', 'partner_name');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_204733_add_index_table_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_204733_add_index_table_contact cannot be reverted.\n";

        return false;
    }
    */
}
