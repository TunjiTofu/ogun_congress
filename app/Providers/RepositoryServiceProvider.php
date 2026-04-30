<?php

namespace App\Providers;

use App\Repositories\CamperRepository;
use App\Repositories\CheckinRepository;
use App\Repositories\Interfaces\CamperRepositoryInterface;
use App\Repositories\Interfaces\CheckinRepositoryInterface;
use App\Repositories\Interfaces\OfflinePaymentRepositoryInterface;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use App\Repositories\OfflinePaymentRepository;
use App\Repositories\RegistrationCodeRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RegistrationCodeRepositoryInterface::class,
            RegistrationCodeRepository::class,
        );

        $this->app->bind(
            CamperRepositoryInterface::class,
            CamperRepository::class,
        );

        $this->app->bind(
            CheckinRepositoryInterface::class,
            CheckinRepository::class,
        );

        $this->app->bind(
            OfflinePaymentRepositoryInterface::class,
            OfflinePaymentRepository::class,
        );
    }
}
