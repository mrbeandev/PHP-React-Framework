<?php

require_once __DIR__ . '/bootstrap.php';

use App\Models\Todo;
use App\Models\Seo;
use App\Models\Setting;

// Sample tasks to seed
$tasks = [
    ['title' => '🚀 Initialize TaskFlow project', 'completed' => true],
    ['title' => '🐘 Setup Eloquent ORM with SQLite', 'completed' => true],
    ['title' => '⚛️ Build high-performance React frontend', 'completed' => false],
    ['title' => '🎨 Apply premium mesh-gradient styling', 'completed' => false],
    ['title' => '🌓 Implement robust theme toggle', 'completed' => true],
    ['title' => '📜 Update documentation & README', 'completed' => false],
];

echo "🌱 Seeding sample todos...\n";

foreach ($tasks as $task) {
    echo "Creating task: {$task['title']}... ";
    Todo::firstOrCreate(['title' => $task['title']], $task);
    echo "✅\n";
}

echo "🌱 Seeding SEO settings...\n";
Setting::firstOrCreate(['key' => 'enable_dynamic_seo'], ['value' => '1']);

$seoItems = [
    [
        'path' => '/',
        'title' => 'TaskFlow - Modern PHP & React Template',
        'description' => 'A high-performance boilerplate for unified PHP and React development.',
        'keywords' => 'php, react, template, eloquent, vite, tailwind',
    ],
    [
        'path' => '/about',
        'title' => 'About TaskFlow',
        'description' => 'Learn more about the TaskFlow unified architecture.',
        'keywords' => 'about, taskflow, project',
    ]
];

foreach ($seoItems as $seo) {
    echo "Creating SEO for: {$seo['path']}... ";
    Seo::firstOrCreate(['path' => $seo['path']], $seo);
    echo "✅\n";
}

echo "🎉 Database seeded successfully!\n";
