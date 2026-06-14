<?php

namespace App\Jobs;

use App\Models\Lookup;
use App\Models\Tenant;
use App\Repositories\ILookupRepository;
use Database\Seeders\CompanySeeder;
use Database\Seeders\LookupsSeeder;
use Database\Seeders\RealstateSiteSettingsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Stancl\Tenancy\Jobs\SeedDatabase;
use Throwable;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Tenant $tenant
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ILookupRepository $lookupRepository): void
    {

        // 1. Crear la base de datos del tenant
        CreateDatabase::dispatchSync($this->tenant);

        // 2. Migrar
        MigrateDatabase::dispatchSync($this->tenant);

        // 3. Seedear
        SeedDatabase::dispatchSync($this->tenant);

        $lookups = $lookupRepository->getLookupsByCategory(categories: ['status']);

        $activo = $lookups['status']
            ->firstWhere('name', 'ACTIVO');

        // 4. Marcar tenant como ACTIVO
        $this->tenant->update(['status_id' => $activo->id]);

        Log::info("Tenant provisionado exitosamente: {$this->tenant->id}");
    }

    public function failed(Throwable $e): void
    {
        Log::error("Error provisionando tenant: {$this->tenant->id}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Limpiar: eliminar dominio y tenant si falló
        try {
            $this->tenant->domains()->delete();
            $this->tenant->delete();
        } catch (Throwable $cleanupError) {
            Log::error("Error limpiando tenant fallido: {$this->tenant->id}", [
                'error' => $cleanupError->getMessage(),
            ]);
        }
    }
}
