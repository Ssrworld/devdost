<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // 'users' टेबल बनाने का कोड यहाँ से शुरू होता है
        
        // 1. टेबल को सेलेक्ट करें (अगर मौजूद नहीं है तो बना देगा)
        $table = $this->table('users');

        // 2. कॉलम्स को एक-एक करके जोड़ें
        $table->addColumn('username', 'string', ['limit' => 50])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password', 'string', ['limit' => 255])
              ->addColumn('user_type', 'string', ['limit' => 20, 'comment' => 'e.g., client, developer'])
              
              // 3. created_at और updated_at के लिए टाइमस्टैम्प्स जोड़ें
              ->addTimestamps() 
              
              // 4. यूनिक इंडेक्स जोड़ें ताकि कोई डुप्लीकेट यूजरनेम या ईमेल न हो
              ->addIndex(['username'], ['unique' => true])
              ->addIndex(['email'], ['unique' => true])
              
              // 5. टेबल को डेटाबेस में बनाएं
              ->create();
    }
}