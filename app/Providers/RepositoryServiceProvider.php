<?php

namespace App\Providers;

use App\Repositories\IAddressRepository;
use App\Repositories\IAccountBankRepository;
use App\Repositories\IContactRepository;
use App\Repositories\IEconomicActivityRepository;
use App\Repositories\IFiscalProfileRepository;
use App\Repositories\ILookupRepository;
use App\Repositories\Implements\AddressRepository;
use App\Repositories\Implements\AccountBankRepository;
use App\Repositories\Implements\ContactRepository;
use App\Repositories\Implements\EconomicActivityRepository;
use App\Repositories\Implements\FiscalProfileRepository;
use App\Repositories\Implements\LookupRepository;
use App\Repositories\Implements\PermissionRepository;
use App\Repositories\Implements\PersonRepository;
use App\Repositories\Implements\RoleRepository;
use App\Repositories\Implements\TaxeTypeRepository;
use App\Repositories\Implements\TenantRepository;
use App\Repositories\Implements\UserRepository;
use App\Repositories\IPermissionRepository;
use App\Repositories\IPersonRepository;
use App\Repositories\IPropertyRepository;
use App\Repositories\IRoleRepository;
use App\Repositories\ITaxeTypeRepository;
use App\Repositories\ITenantRepository;
use App\Repositories\IUserRepository;
use App\Services\Implements\PropertyService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(ITenantRepository::class, TenantRepository::class);
        $this->app->bind(IRoleRepository::class, RoleRepository::class);
        $this->app->bind(IPermissionRepository::class, PermissionRepository::class);
        $this->app->bind(IPersonRepository::class, PersonRepository::class);
        $this->app->bind(IFiscalProfileRepository::class, FiscalProfileRepository::class);
        $this->app->bind(ILookupRepository::class, LookupRepository::class);
        $this->app->bind(IAccountBankRepository::class, AccountBankRepository::class);
        $this->app->bind(IAddressRepository::class, AddressRepository::class);
        $this->app->bind(IContactRepository::class, ContactRepository::class);
        $this->app->bind(IEconomicActivityRepository::class, EconomicActivityRepository::class);
        $this->app->bind(ITaxeTypeRepository::class, TaxeTypeRepository::class);
        $this->app->bind(IPropertyRepository::class, PropertyService::class);
    }
}
