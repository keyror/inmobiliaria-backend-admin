<?php

namespace Database\Seeders;

use App\Models\Lookup;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

// php artisan db:seed --class=LocalDemoTenantSeeder
// Requiere: 127.0.0.1 demo.localhost en el hosts de Windows
// Backend con: php artisan serve --host=0.0.0.0 --port=8000

class LocalDemoTenantSeeder extends Seeder
{
    private const string DOMAIN = 'demo.inmobiliaria.com';

    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $tenant = $this->findOrCreateTenant();

        $self = $this;
        $tenant->run(function () use ($self): void {
            config(['activitylog.enabled' => false]);

            $self->call(TenantDatabaseSeeder::class);
            $self->call(PersonsTableSeeder::class);
            $self->call(PropertiesTableSeeder::class);
            $self->call(FeaturedPropertiesSeeder::class);
            $self->call(DocumentTemplatesSeeder::class);
            $self->call(TemplateSectionsSeeder::class);
        });

        $this->command->info('✓ Tenant local listo: '.self::DOMAIN);
    }

    private function findOrCreateTenant(): Tenant
    {
        $existing = Tenant::query()
            ->whereHas('domains', fn ($q) => $q->where('domain', self::DOMAIN))
            ->first();

        if ($existing) {
            $this->command->info('Tenant local ya existe (omitiendo creación de BD): '.self::DOMAIN);

            return $existing;
        }

        $id = Str::uuid()->toString();

        $statusId = Lookup::query()
            ->where('category', 'status')
            ->where('name', 'ACTIVO')
            ->value('id');

        $planId = Plan::query()
            ->where('name', 'Profesional')
            ->value('id');

        $tenant = Tenant::create([
            'id' => $id,
            'name' => 'Inmobiliaria Demo Local',
            'email' => 'demo@localhost.test',
            'domain' => self::DOMAIN,
            'plan_id' => $planId,
            'status_id' => $statusId,
            'subscription_ends_at' => now()->addYear(),
            'tenancy_db_name' => 'realstate_local_'.$id,
        ]);

        $tenant->createDomain(['domain' => self::DOMAIN]);

        $this->command->info('Creando base de datos del tenant local...');
        CreateDatabase::dispatchSync($tenant);

        $this->command->info('Migrando base de datos del tenant local...');
        MigrateDatabase::dispatchSync($tenant);

        return $tenant;
    }
}
