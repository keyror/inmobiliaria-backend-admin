<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lookup;
use App\Models\PublishChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CentralCompanySeeder extends Seeder
{
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $company = Company::query()->updateOrCreate(
            ['nit' => '901123456-7'],
            [
                'company_name' => 'VELTRA S.A.S.',
                'tradename' => 'VELTRA',
                'is_active' => true,
            ],
        );

        $this->seedLogo($company);
        $this->seedContact($company);
        $this->seedAddress($company);
        $this->seedSocialLinks($company);
    }

    private function seedLogo(Company $company): void
    {
        if ($company->logo()->exists()) {
            return;
        }

        $filePath = 'realsite-images/logo/logo.png';
        $disk = Storage::disk('public');
        $source = public_path('logo.png');

        if (! $disk->exists($filePath) && file_exists($source)) {
            $disk->put($filePath, file_get_contents($source));
        }

        if (! $disk->exists($filePath)) {
            return;
        }

        $company->logo()->create([
            'id' => Str::uuid(),
            'file_name' => 'central-logo.png',
            'file_path' => $filePath,
            'file_extension' => 'png',
            'mime_type' => $disk->mimeType($filePath) ?? 'image/png',
            'file_size' => $disk->size($filePath) ?? 0,
            'sort_order' => 0,
            'is_cover' => true,
            'is_public' => true,
        ]);
    }

    private function seedContact(Company $company): void
    {
        $company->contacts()->firstOrCreate(
            ['is_principal' => true],
            [
                'id' => Str::uuid(),
                'phone' => '+57 (601) 000-0000',
                'mobile' => '+57 300 000 0000',
                'email' => 'contacto@veltra.co',
            ]
        );
    }

    private function seedAddress(Company $company): void
    {
        if ($company->addresses()->exists()) {
            return;
        }

        $bogota = Lookup::where('alias', 'BOGOTA')->value('id');
        $cundinamarca = Lookup::where('alias', 'CUNDINAMARCA')->value('id');
        $colombia = Lookup::where('alias', 'COLOMBIA')->where('category', 'country')->value('id');

        if (! $bogota || ! $cundinamarca || ! $colombia) {
            return;
        }

        $company->addresses()->create([
            'id' => Str::uuid(),
            'address' => 'Calle 0 # 0 - 00',
            'city_id' => $bogota,
            'department_id' => $cundinamarca,
            'country_id' => $colombia,
            'is_principal' => true,
        ]);
    }

    private function seedSocialLinks(Company $company): void
    {
        $defaults = [
            'FACEBOOK' => 'https://facebook.com/veltra',
            'INSTAGRAM' => 'https://instagram.com/veltra',
            'WHATSAPP' => 'https://wa.me/573000000000',
            'LINKEDIN' => 'https://linkedin.com/company/veltra',
            'SITIO_WEB' => 'https://veltra.co',
        ];

        $channels = Lookup::whereIn('alias', array_keys($defaults))
            ->pluck('id', 'alias');

        foreach ($defaults as $alias => $link) {
            $channelId = $channels->get($alias);

            if (! $channelId) {
                continue;
            }

            PublishChannel::query()->firstOrCreate(
                ['company_id' => $company->id, 'channel_id' => $channelId],
                [
                    'id' => Str::uuid(),
                    'external_link' => $link,
                ]
            );
        }
    }
}
