<?php

namespace App\Providers;

use App\Services\IAuditService;
use App\Services\IAuthenticationService;
use App\Services\ICompanyService;
use App\Services\IDashboardService;
use App\Services\IDocumentService;
use App\Services\IFiscalProfileService;
use App\Services\IImageService;
use App\Services\ILookupService;
use App\Services\Implements\AuditService;
use App\Services\Implements\AuthenticationService;
use App\Services\Implements\CompanyService;
use App\Services\Implements\DashboardService;
use App\Services\Implements\DocumentService;
use App\Services\Implements\FiscalProfileService;
use App\Services\Implements\ImageService;
use App\Services\Implements\LookupService;
use App\Services\Implements\PermissionService;
use App\Services\Implements\PersonService;
use App\Services\Implements\PlanLimitService;
use App\Services\Implements\PlanService;
use App\Services\Implements\PropertyService;
use App\Services\Implements\PublicCompanyService;
use App\Services\Implements\PublicPropertyService;
use App\Services\Implements\PublicRealstateSiteService;
use App\Services\Implements\RealstateTemplateManagementService;
use App\Services\Implements\RentService;
use App\Services\Implements\ReportTemplateService;
use App\Services\Implements\RoleService;
use App\Services\Implements\SearchService;
use App\Services\Implements\TemplateSectionService;
use App\Services\Implements\TenantService;
use App\Services\Implements\TenantUserService;
use App\Services\Implements\UserService;
use App\Services\IPermissionService;
use App\Services\IPersonService;
use App\Services\IPlanLimitService;
use App\Services\IPlanService;
use App\Services\IPropertyService;
use App\Services\IPublicCompanyService;
use App\Services\IPublicPropertyService;
use App\Services\IPublicRealstateSiteService;
use App\Services\IRealstateTemplateManagementService;
use App\Services\IRentService;
use App\Services\IReportTemplateService;
use App\Services\IRoleService;
use App\Services\ISearchService;
use App\Services\ITemplateSectionService;
use App\Services\ITenantService;
use App\Services\ITenantUserService;
use App\Services\IUserService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->app->bind(IAuditService::class, AuditService::class);
        $this->app->bind(ITenantUserService::class, TenantUserService::class);
        $this->app->bind(ISearchService::class, SearchService::class);
        $this->app->bind(IDashboardService::class, DashboardService::class);
        $this->app->bind(IAuthenticationService::class, AuthenticationService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(ITenantService::class, TenantService::class);
        $this->app->bind(IRoleService::class, RoleService::class);
        $this->app->bind(IPermissionService::class, PermissionService::class);
        $this->app->bind(IPersonService::class, PersonService::class);
        $this->app->bind(IFiscalProfileService::class, FiscalProfileService::class);
        $this->app->bind(ILookupService::class, LookupService::class);
        $this->app->bind(IPropertyService::class, PropertyService::class);
        $this->app->bind(IRentService::class, RentService::class);
        $this->app->bind(IDocumentService::class, DocumentService::class);
        $this->app->bind(ITemplateSectionService::class, TemplateSectionService::class);
        $this->app->bind(IPublicCompanyService::class, PublicCompanyService::class);
        $this->app->bind(IPublicRealstateSiteService::class, PublicRealstateSiteService::class);
        $this->app->bind(IPublicPropertyService::class, PublicPropertyService::class);
        $this->app->bind(IRealstateTemplateManagementService::class, RealstateTemplateManagementService::class);
        $this->app->bind(IImageService::class, ImageService::class);
        $this->app->bind(ICompanyService::class, CompanyService::class);
        $this->app->bind(IPlanService::class, PlanService::class);
        $this->app->bind(IPlanLimitService::class, PlanLimitService::class);
        $this->app->bind(IReportTemplateService::class, ReportTemplateService::class);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('public-properties', function (Request $request): Limit {
            return $this->limitPerMinute('public_properties_per_minute')
                ->by('public-properties:'.$request->ip());
        });

        RateLimiter::for('public-property-show', function (Request $request): Limit {
            return $this->limitPerMinute('public_property_show_per_minute')
                ->by('public-property-show:'.$request->ip());
        });

        RateLimiter::for('public-property-contact', function (Request $request): Limit {
            return $this->limitPerMinute('public_property_contact_per_minute')
                ->by('public-property-contact:'.$request->ip());
        });

        RateLimiter::for('public-company-contact', function (Request $request): Limit {
            return $this->limitPerMinute('public_company_contact_per_minute')
                ->by('public-company-contact:'.$request->ip());
        });

        RateLimiter::for('lookups', function (Request $request): Limit {
            return $this->limitPerMinute('lookups_per_minute')
                ->by('lookups:'.$request->ip());
        });

        RateLimiter::for('login', function (Request $request): Limit {
            return $this->limitPerMinute('login_per_minute')
                ->by('login:'.$request->ip().':'.$request->string('email')->lower());
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            return $this->limitPerMinute('password_reset_per_minute')
                ->by('password-reset:'.$request->ip().':'.$request->string('email')->lower());
        });

        RateLimiter::for('authenticated-api', function (Request $request): Limit {
            return $this->limitPerMinute('authenticated_api_per_minute')
                ->by('authenticated-api:'.($request->user()?->getAuthIdentifier() ?: $request->ip()));
        });

        RateLimiter::for('token-refresh', function (Request $request): Limit {
            return $this->limitPerMinute('token_refresh_per_minute')
                ->by('token-refresh:'.$request->ip());
        });

        RateLimiter::for('image-uploads', function (Request $request): Limit {
            return $this->limitPerMinute('image_uploads_per_minute')
                ->by('image-uploads:'.($request->user()?->getAuthIdentifier() ?: $request->ip()));
        });
    }

    private function limitPerMinute(string $key): Limit
    {
        if (! config('rate_limits.enabled')) {
            return Limit::none();
        }

        return Limit::perMinute((int) config("rate_limits.{$key}"));
    }
}
