<?php

use yii\db\Migration;

/**
 * Class m210106_165055_add_table_warehouse_storage
 */
class m210106_165055_add_table_warehouse_storage extends Migration
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
        $this->createTable('{{%warehouse_storage}}', [
            'id' => $this->primaryKey(),
            'po_code' => $this->string(255),
            'warehouse_id' => $this->integer(),
            'qty' => $this->integer()->defaultValue(0),
            'sku' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210106_165055_add_table_warehouse_storage cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210106_165055_add_table_warehouse_storage cannot be reverted.\n";

        return false;
    }
    */
}
