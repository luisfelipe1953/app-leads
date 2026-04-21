<?php

namespace App\Providers;

use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\LeadRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
    }
}
