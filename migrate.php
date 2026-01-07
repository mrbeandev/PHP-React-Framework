<?php

require_once __DIR__ . '/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasTable('todos')) {
    Capsule::schema()->create('todos', function ($table) {
        $table->increments('id');
        $table->string('title');
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
    echo "Table 'todos' created successfully.\n";
}

if (!Capsule::schema()->hasTable('seo')) {
    Capsule::schema()->create('seo', function ($table) {
        $table->increments('id');
        $table->string('path')->unique();
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        $table->string('keywords')->nullable();
        $table->string('og_image')->nullable();
        $table->timestamps();
    });
    echo "Table 'seo' created successfully.\n";
}

if (!Capsule::schema()->hasTable('settings')) {
    Capsule::schema()->create('settings', function ($table) {
        $table->increments('id');
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });
    echo "Table 'settings' created successfully.\n";
}
