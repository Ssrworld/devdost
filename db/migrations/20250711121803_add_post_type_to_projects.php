<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPostTypeToProjects extends AbstractMigration
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
        // 'projects' टेबल को चुनें
        $table = $this->table('projects');

        // नया कॉलम 'post_type' जोड़ें
        $table->addColumn('post_type', 'enum', [
            'values' => ['project', 'job'],  // केवल ये दो वैल्यूज ही स्वीकार की जाएंगी
            'default' => 'project',          // डिफ़ॉल्ट रूप से, सभी मौजूदा पोस्ट्स 'project' होंगी
            'null' => false,                 // यह कॉलम खाली नहीं हो सकता
            'after' => 'user_id'             // इस कॉलम को 'user_id' कॉलम के बाद रखें (वैकल्पिक)
        ])
        ->update(); // टेबल में बदलावों को लागू करें
    }
}