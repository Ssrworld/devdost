<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProjectsTable extends AbstractMigration
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
        // 'projects' टेबल बनाने का कोड
        $table = $this->table('projects');
        
        $table->addColumn('user_id', 'integer', ['signed' => false]) // Foreign key के लिए unsigned होना बेहतर है
              ->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('description', 'text')
              ->addColumn('budget', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true]) // बजट वैकल्पिक हो सकता है
              ->addColumn('status', 'string', ['limit' => 20, 'default' => 'open', 'comment' => 'open, in_progress, completed, cancelled']) // स्थिति
              
              // created_at और updated_at के लिए टाइमस्टैम्प्स
              ->addTimestamps()
              
              // user_id को users टेबल की id से जोड़ना
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}