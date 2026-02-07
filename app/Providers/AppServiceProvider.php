<?php

namespace App\Providers;

use App\Controllers\Web\FrontendController;
use App\Core\Container\Container;

class AppServiceProvider
{
    public function __construct(private readonly string $distPath)
    {
    }

    public function register(Container $container): void
    {
        $container->singleton(FrontendController::class, fn () => new FrontendController($this->distPath));
    }
}
