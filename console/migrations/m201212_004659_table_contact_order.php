<?php

use yii\db\Migration;

/**
 * Class m201212_004659_table_contact_order
 */
class m201212_004659_table_contact_order extends Migration
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
        $this->createTable('{{%orders_contact}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'phone' => $this->string(25)->notNull(),
            'code' => $this->string(255),
            'address' => $this->string(255)->notNull(),
            'zipcode' => $this->string(255)->notNull(),
            'shipping_cost' => $this->double(15.2)->defaultValue(0),
            'payment_method' => $this->integer(),
            'email' => $this->string(255)->null(),
            'city' => $this->string(255)->null(),
            'district' => $this->string(255)->null(),
            'order_source' => $this->string()->null(),
            'note' => $this->string(255)->null(),
            'vendor_note' => $this->string(255)->null(),
            'status' => $this->string(100)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%orders_contact_sku}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'sku' => $this->string(255)->notNull(),
            'qty' => $this->integer()->defaultValue(1),
            'price' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%contacts_source}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%payments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);


        $this->addForeignKey(
            'contact_order_fk_sku',
            'orders_contact_sku',
            'order_id',
            'orders_contact',
            'id',
            'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201212_004659_table_contact_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201212_004659_table_contact_order cannot be reverted.\n";

        return false;
    }
    */
}
