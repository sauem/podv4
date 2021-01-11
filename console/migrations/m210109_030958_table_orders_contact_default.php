<?php

use yii\db\Migration;

/**
 * Class m210109_030958_table_orders_contact_default
 */
class m210109_030958_table_orders_contact_default extends Migration
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
        $this->createTable('{{%orders_example}}', [
            'id' => $this->primaryKey(),
            'category' => $this->string(255),
            'option' => $this->text()->null(),
            'total_bill' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%orders_example_item}}', [
            'id' => $this->primaryKey(),
            'order_example_id' => $this->integer(),
            'sku' => $this->string(),
            'qty' => $this->integer()->defaultValue(0),
            'price' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey("order_examp_fk_sku_item",
            "orders_example_item",
            "order_example_id",
            "orders_example",
            "id",
            "CASCADE"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210109_030958_table_orders_contact_default cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210109_030958_table_orders_contact_default cannot be reverted.\n";

        return false;
    }
    */
}
