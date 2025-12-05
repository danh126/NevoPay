<?php

namespace App\Providers;

use App\Repositories\AuditLogRepository;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use App\Repositories\Interfaces\OtpCodeRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\OtpCodeRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
        $this->app->bind(OtpCodeRepositoryInterface::class, OtpCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
