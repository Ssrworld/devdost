<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriesTable extends AbstractMigration
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
        // 'categories' नाम की एक नई टेबल बनाएं
        $table = $this->table('categories');

        // कॉलम्स को परिभाषित करें
        $table->addColumn('name', 'string', [
                'limit' => 100, 
                'comment' => 'The display name of the category (e.g., WordPress Plugins)'
              ])
              ->addColumn('slug', 'string', [
                'limit' => 100, 
                'comment' => 'A URL-friendly version of the name (e.g., wordpress-plugins)'
              ])
              
              // एक इंडेक्स जोड़ें ताकि हम स्लग को तेजी से खोज सकें और यह सुनिश्चित कर सकें कि यह यूनिक हो
              ->addIndex(['slug'], ['unique' => true])
              
              // अंत में, टेबल को बनाएं
              ->create();
    }
}