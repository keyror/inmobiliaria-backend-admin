<?php

namespace Database\Seeders;

use App\Models\Lookup;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

class DemoTenantSeeder extends Seeder
{
    private const string DOMAIN = 'demo.inmobiliariaapp.duckdns.org';

    private const string commandExec = 'php artisan db:seed --class=DemoTenantSeeder';

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

        $this->command->info('✓ Demo tenant listo: '.self::DOMAIN);
    }

    private function findOrCreateTenant(): Tenant
    {
        $existing = Tenant::query()
            ->whereHas('domains', fn ($q) => $q->where('domain', self::DOMAIN))
            ->first();

        if ($existing) {
            $this->command->info('Tenant demo ya existe (omitiendo creación de BD): '.self::DOMAIN);

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
            'name' => 'Demo Inmobiliaria',
            'email' => 'demo@inmobiliariaapp.duckdns.org',
            'domain' => self::DOMAIN,
            'plan_id' => $planId,
            'status_id' => $statusId,
            'subscription_ends_at' => now()->addYear(),
            'tenancy_db_name' => 'realstate_di_'.$id,
        ]);

        $tenant->createDomain(['domain' => self::DOMAIN]);

        $this->command->info('Creando base de datos del tenant demo...');
        CreateDatabase::dispatchSync($tenant);

        $this->command->info('Migrando base de datos del tenant demo...');
        MigrateDatabase::dispatchSync($tenant);

        return $tenant;
    }
}
