<?php

use yii\db\Migration;

/**
 * Class m201229_133911_add_table_googlesheet
 */
class m201229_133911_add_table_googlesheet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%contacts_sheet}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer(),
            'sku' => $this->string(255),
            'sheet_id' => $this->text(),
            'country' => $this->string(255),
            'contact_source' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_133911_add_table_googlesheet cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_133911_add_table_googlesheet cannot be reverted.\n";

        return false;
    }
    */
}
