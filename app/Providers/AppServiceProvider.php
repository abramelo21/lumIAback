<?php

namespace App\Providers;

use App\Services\AI\AIProviderInterface;
use App\Services\AI\Providers\GeminiProvider;
use Illuminate\Support\ServiceProvider;
use Smalot\PdfParser\Parser;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AIProviderInterface::class, GeminiProvider::class);
        $this->app->bind(Parser::class, fn () => new Parser);
    }

    public function boot(): void
    {
        //
    }
}
