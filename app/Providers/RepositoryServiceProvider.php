<?php

namespace App\Providers;

use App\Repositories\IAccountBankRepository;
use App\Repositories\IAddressRepository;
use App\Repositories\IAuditRepository;
use App\Repositories\ICompanyRepository;
use App\Repositories\ICompanySettingRepository;
use App\Repositories\IContactRepository;
use App\Repositories\IDashboardRepository;
use App\Repositories\IEconomicActivityRepository;
use App\Repositories\IFiscalProfileRepository;
use App\Repositories\IImageRepository;
use App\Repositories\ILookupRepository;
use App\Repositories\Implements\AccountBankRepository;
use App\Repositories\Implements\AddressRepository;
use App\Repositories\Implements\AuditRepository;
use App\Repositories\Implements\CompanyRepository;
use App\Repositories\Implements\CompanySettingRepository;
use App\Repositories\Implements\ContactRepository;
use App\Repositories\Implements\DashboardRepository;
use App\Repositories\Implements\EconomicActivityRepository;
use App\Repositories\Implements\FiscalProfileRepository;
use App\Repositories\Implements\ImageRepository;
use App\Repositories\Implements\LookupRepository;
use App\Repositories\Implements\PermissionRepository;
use App\Repositories\Implements\PersonRepository;
use App\Repositories\Implements\PlanRepository;
use App\Repositories\Implements\PropertyRepository;
use App\Repositories\Implements\PublicPropertyRepository;
use App\Repositories\Implements\RealstateSiteSettingRepository;
use App\Repositories\Implements\RoleRepository;
use App\Repositories\Implements\SearchRepository;
use App\Repositories\Implements\TaxeTypeRepository;
use App\Repositories\Implements\TenantRepository;
use App\Repositories\Implements\TenantUserRepository;
use App\Repositories\Implements\UserRepository;
use App\Repositories\IPermissionRepository;
use App\Repositories\IPersonRepository;
use App\Repositories\IPlanRepository;
use App\Repositories\IPropertyRepository;
use App\Repositories\IPublicPropertyRepository;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Repositories\IRoleRepository;
use App\Repositories\ISearchRepository;
use App\Repositories\ITaxeTypeRepository;
use App\Repositories\ITenantRepository;
use App\Repositories\ITenantUserRepository;
use App\Repositories\IUserRepository;
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
        $this->app->bind(IAuditRepository::class, AuditRepository::class);
        $this->app->bind(ITenantUserRepository::class, TenantUserRepository::class);
        $this->app->bind(ISearchRepository::class, SearchRepository::class);
        $this->app->bind(IDashboardRepository::class, DashboardRepository::class);
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
        $this->app->bind(IPropertyRepository::class, PropertyRepository::class);
        $this->app->bind(IPublicPropertyRepository::class, PublicPropertyRepository::class);
        $this->app->bind(IRealstateSiteSettingRepository::class, RealstateSiteSettingRepository::class);
        $this->app->bind(IImageRepository::class, ImageRepository::class);
        $this->app->bind(ICompanyRepository::class, CompanyRepository::class);
        $this->app->bind(ICompanySettingRepository::class, CompanySettingRepository::class);
        $this->app->bind(IPlanRepository::class, PlanRepository::class);
    }
}
