<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Eloquent\AlertRepository;

use App\Repositories\Contracts\BencanaRepositoryInterface;
use App\Repositories\Eloquent\BencanaRepository;

use App\Repositories\Contracts\LaporanRepositoryInterface;
use App\Repositories\Eloquent\LaporanRepository;

use App\Repositories\Contracts\LokasiRepositoryInterface;
use App\Repositories\Eloquent\LokasiRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AlertRepositoryInterface::class, AlertRepository::class);
        $this->app->bind(BencanaRepositoryInterface::class, BencanaRepository::class);
        $this->app->bind(LaporanRepositoryInterface::class, LaporanRepository::class);
        $this->app->bind(LokasiRepositoryInterface::class, LokasiRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
