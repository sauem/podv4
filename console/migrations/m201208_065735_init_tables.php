<?php

use yii\db\Migration;

/**
 * Class m201208_065735_init_tables
 */
class m201208_065735_init_tables extends Migration
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

        $this->alterColumn('zipcode_country', 'district', $this->string(255)->null());
        $this->alterColumn('zipcode_country', 'address', $this->string(255)->null());
        $this->alterColumn('zipcode_country', 'city', $this->string(255)->null());

        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'country' => $this->string()->null(),
            'partner_id' => $this->integer()->notNull(),
            'description' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'sku' => $this->string(255)->unique(),
            'category_id' => $this->integer()->notNull(),
            'partner_id' => $this->integer()->notNull(),
            'size' => $this->string()->null(),
            'weight' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%products_price}}', [
            'id' => $this->primaryKey(),
            'sku' => $this->string(255),
            'qty' => $this->integer()->null(),
            'price' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%contacts}}', [
            'id' => $this->primaryKey(),
            'register_time' => $this->integer()->notNull(),
            'code' => $this->string(255)->notNull(),
            'phone' => $this->string(25)->notNull(),
            'name' => $this->string(255)->null(),
            'email' => $this->string(255)->null(),
            'address' => $this->string(255)->null(),
            'zipcode' => $this->string(255)->null(),
            'option' => $this->text()->null(),
            'ip' => $this->string(255)->null(),
            'note' => $this->string(255)->null(),
            'partner' => $this->string(255)->null(),
            'hash_key' => $this->string(255)->null(),
            'status' => $this->string(50)->null(),
            'country' => $this->string(255)->null(),
            'utm_source' => $this->string(255)->null(),
            'utm_medium' => $this->string(255)->null(),
            'utm_content' => $this->string(255)->null(),
            'utm_term' => $this->string(255)->null(),
            'utm_campaign' => $this->string(255)->null(),
            'link' => $this->string(255)->null(),
            'short_link' => $this->string(255)->null(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%contacts_assignment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'phone' => $this->string(25),
            'status' => $this->string(25),
            'country' => $this->string(25),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%contacts_log_status}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(255),
            'user_id' => $this->integer(),
            'phone' => $this->string(25),
            'status' => $this->string(50),
            'sale_note' => $this->string(255)->null(),
            'customer_note' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_065735_init_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_065735_init_tables cannot be reverted.\n";

        return false;
    }
    */
}
