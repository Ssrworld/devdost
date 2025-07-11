<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAvatarToUsersTable extends AbstractMigration
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
        // 'users' टेबल को सेलेक्ट करें ताकि हम उसमें बदलाव कर सकें
        $table = $this->table('users');
        
        // नया 'avatar' कॉलम जोड़ें
        $table->addColumn('avatar', 'string', [
            'limit' => 255, 
            'null' => true, // यह कॉलम खाली हो सकता है
            'after' => 'user_type', // यह 'user_type' कॉलम के बाद आएगा
            'comment' => 'Profile picture filename'
        ])
        ->update(); // टेबल में बदलावों को सेव करें
    }
}