<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProfilesTable extends AbstractMigration
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
        // 'profiles' टेबल बनाने का कोड
        $table = $this->table('profiles');
        
        $table->addColumn('user_id', 'integer', ['signed' => false])
              ->addColumn('tagline', 'string', ['limit' => 255, 'null' => true, 'comment' => 'e.g., Full-Stack Web Developer'])
              ->addColumn('bio', 'text', ['null' => true, 'comment' => 'About the developer'])
              ->addColumn('skills', 'text', ['null' => true, 'comment' => 'Comma-separated values, e.g., PHP,Laravel,Vue.js'])
              ->addColumn('location', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('website_url', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('github_url', 'string', ['limit' => 255, 'null' => true])
              
              // created_at और updated_at के लिए
              ->addTimestamps()
              
              // हर यूजर की एक ही प्रोफाइल होगी, यह सुनिश्चित करता है
              ->addIndex(['user_id'], ['unique' => true])

              // user_id को users टेबल की id से जोड़ना
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}