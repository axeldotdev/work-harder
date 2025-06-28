<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;

class AppServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void {}

    public function boot(): void
    {
        $this->bootCommands();
        $this->bootDates();
        $this->bootExceptions();
        $this->bootModels();
        $this->bootPassword();
        $this->bootRoutes();
        $this->bootUrl();
        $this->bootVite();
    }

    private function bootCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function bootDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function bootExceptions(): void
    {
        RequestException::dontTruncate();
    }

    private function bootModels(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    private function bootPassword(): void
    {
        Password::defaults(fn () => Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised());
    }

    private function bootRoutes(): void {}

    private function bootUrl(): void
    {
        URL::forceScheme('https');
    }

    private function bootVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}
