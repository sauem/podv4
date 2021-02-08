<?php

use yii\db\Migration;

/**
 * Class m210208_052502_add_index_assingment
 */
class m210208_052502_add_index_assingment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_assignment', 'contacts_assignment', ['phone', 'user_id', 'status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210208_052502_add_index_assingment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_052502_add_index_assingment cannot be reverted.\n";

        return false;
    }
    */
}
