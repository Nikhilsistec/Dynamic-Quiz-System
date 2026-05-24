<?php

namespace App\Providers;

use App\Quiz\Handlers\BinaryHandler;
use App\Quiz\Handlers\MultipleChoiceHandler;
use App\Quiz\Handlers\NumberHandler;
use App\Quiz\Handlers\SingleChoiceHandler;
use App\Quiz\Handlers\TextHandler;
use App\Quiz\QuestionTypeRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuestionTypeRegistry::class, function () {
            $registry = new QuestionTypeRegistry();
            $registry->register(new BinaryHandler());
            $registry->register(new SingleChoiceHandler());
            $registry->register(new MultipleChoiceHandler());
            $registry->register(new NumberHandler());
            $registry->register(new TextHandler());
            return $registry;
        });
    }

    public function boot(): void
    {
        //
    }
}
