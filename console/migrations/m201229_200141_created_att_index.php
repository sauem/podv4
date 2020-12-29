<?php

use yii\db\Migration;

/**
 * Class m201229_200141_created_att_index
 */
class m201229_200141_created_att_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('chash_key', 'contacts', 'hash_key', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_200141_created_att_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_200141_created_att_index cannot be reverted.\n";

        return false;
    }
    */
}
