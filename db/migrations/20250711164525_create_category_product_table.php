<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryProductTable extends AbstractMigration
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
        // टेबल का नाम सिंगुलर और अल्फाबेटिकल ऑर्डर में होता है: 'category_product'
        // इस टेबल में अपनी खुद की 'id' नहीं होगी, इसलिए हम इसे अक्षम कर देते हैं
        // और 'category_id' और 'product_id' के संयोजन को प्राइमरी की बनाते हैं
        $table = $this->table('category_product', ['id' => false, 'primary_key' => ['category_id', 'product_id']]);

        // फॉरेन की (foreign key) कॉलम्स को परिभाषित करें
        $table->addColumn('category_id', 'integer', ['signed' => false])
              ->addColumn('product_id', 'integer', ['signed' => false])
              
              // Foreign Keys constraints जो डेटा की संगति सुनिश्चित करते हैं
              ->addForeignKey('category_id', 'categories', 'id', [
                    'delete' => 'CASCADE', 
                    'update' => 'NO_ACTION',
                    'constraint' => 'fk_category_product_category_id' // कंस्ट्रेंट का एक यूनिक नाम
                ])
              ->addForeignKey('product_id', 'products', 'id', [
                    'delete' => 'CASCADE', 
                    'update' => 'NO_ACTION',
                    'constraint' => 'fk_category_product_product_id' // कंस्ट्रेंट का एक यूनिक नाम
                ])
              
              ->create();
    }
}