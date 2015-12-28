<?php

use yii\db\Migration;

class m151228_071437_Reviews extends Migration
{
    public function up() {
        $this->insert('{{%configurable}}', [
            'module' => 'ReviewsExt',
            'section_name' => 'Reviews Extension',
            'display_in_config' => 0
        ]);
    }

    public function down() {
        $this->delete('{{%configurable}}', ['module' => 'ReviewsExt']);
    }

}
