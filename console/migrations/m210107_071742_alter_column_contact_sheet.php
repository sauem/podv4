<?php

use yii\db\Migration;

/**
 * Class m210107_071742_alter_column_contact_sheet
 */
class m210107_071742_alter_column_contact_sheet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("contacts_sheet", "sku", $this->integer()->null());
        $this->renameColumn("contacts_sheet", "sku", 'category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_071742_alter_column_contact_sheet cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_071742_alter_column_contact_sheet cannot be reverted.\n";

        return false;
    }
    */
}
