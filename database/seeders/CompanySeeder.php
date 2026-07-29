<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Image;
use App\Models\Lookup;
use App\Models\Person;
use App\Models\PublishChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CompanySeeder extends Seeder
{
    /**
     * @throws \Throwable
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $company = Company::query()->updateOrCreate(
                ['nit' => '901123456-7'],
                [
                    'company_name' => 'VELTRA S.A.S.',
                    'tradename' => 'VELTRA',
                ],
            );

            $representative = $this->seedRepresentative($company);

            $company->update([
                'legal_representative_id' => $representative->id,
                'person_attendant_id' => $representative->id,
            ]);

            $company->contacts()->updateOrCreate(
                ['email' => 'info@veltra.test'],
                [
                    'phone' => '6013900000',
                    'mobile' => '3001234567',
                    'is_principal' => true,
                ],
            );

            $this->seedAddress($company);

            CompanySetting::query()->updateOrCreate(
                ['company_id' => $company->id],
                ['text_case_mode' => null],
            );

            $this->seedAccountBanks($company);
            $this->seedLogo($company);
            $this->seedPublishChannels($company);
        });
    }

    private function seedRepresentative(Company $company): Person
    {
        return Person::query()->updateOrCreate(
            ['document_number' => '79123456'],
            [
                'company_id' => $company->id,
                'first_name' => 'Carlos',
                'last_name' => 'Ramírez Gómez',
                'full_name' => 'Carlos Ramírez Gómez',
                'document_type_id' => $this->lookupByAlias('document_type', 'CC'),
                'document_from_id' => $this->lookupId('city', 'Bogotá'),
                'organization_type_id' => $this->lookupByAlias('organization_type', 'PN'),
                'gender_type_id' => $this->lookupByAlias('gender', 'M'),
            ],
        );
    }

    private function seedAddress(Company $company): void
    {
        $company->addresses()->updateOrCreate(
            ['is_principal' => true],
            [
                'address' => 'Calle 100 # 15-20',
                'city_id' => $this->lookupId('city', 'Bogotá'),
                'department_id' => $this->lookupId('department', 'Cundinamarca'),
                'country_id' => $this->lookupId('country', 'Colombia'),
                'zip_code' => '110111',
                'sector' => 'Chicó',
                'complement' => 'Oficina 501',
                'stratum_id' => $this->lookupByAlias('stratum', '6'),
                'via_type_id' => $this->lookupByAlias('road_type', 'CL'),
                'via_number' => '100',
                'number2' => '15',
                'number3' => '20',
            ],
        );
    }

    private function seedAccountBanks(Company $company): void
    {
        $bankId = Lookup::query()->where('category', 'banks')->first()?->id;
        $accountTypeId = Lookup::query()->where('category', 'account_banks')->first()?->id;

        if (! $bankId || ! $accountTypeId) {
            return;
        }

        $company->accountBanks()->updateOrCreate(
            ['account_number' => '0123456789'],
            [
                'bank_id' => $bankId,
                'account_type_id' => $accountTypeId,
                'is_principal' => true,
            ],
        );
    }

    private function seedPublishChannels(Company $company): void
    {
        $statusId = Lookup::query()
            ->where('category', 'status')
            ->where('name', 'ACTIVO')
            ->value('id');

        $socialNetworks = [
            ['alias' => 'FACEBOOK',  'url' => 'https://www.facebook.com/veltra'],
            ['alias' => 'INSTAGRAM', 'url' => 'https://www.instagram.com/veltra'],
            ['alias' => 'X_TWITTER', 'url' => 'https://twitter.com/veltra'],
            ['alias' => 'WHATSAPP',  'url' => 'https://wa.me/573001234567'],
        ];

        foreach ($socialNetworks as $social) {
            $channelId = Lookup::query()
                ->where('category', 'publish_channel')
                ->where('alias', $social['alias'])
                ->value('id');

            if (! $channelId) {
                continue;
            }

            PublishChannel::query()->updateOrCreate(
                ['company_id' => $company->id, 'channel_id' => $channelId],
                [
                    'external_link' => $social['url'],
                    'status_id' => $statusId,
                ],
            );
        }
    }

    private function lookupId(string $category, string $name): string
    {
        return Lookup::query()
            ->where('category', $category)
            ->where('name', $name)
            ->valueOrFail('id');
    }

    private function lookupByAlias(string $category, string $alias): string
    {
        return Lookup::query()
            ->where('category', $category)
            ->where('alias', $alias)
            ->valueOrFail('id');
    }

    private function seedLogo(Company $company): void
    {
        $sourcePath = public_path('logo.png');
        $storagePath = storage_path('app/public/logo.png');

        if (! File::exists($sourcePath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($storagePath));
        File::copy($sourcePath, $storagePath);

        [$width, $height] = getimagesize($sourcePath);

        Image::query()->updateOrCreate(
            [
                'imageable_id' => $company->id,
                'imageable_type' => Company::class,
                'file_path' => 'logo.png',
            ],
            [
                'title' => 'Logo VELTRA',
                'description' => 'Logo principal de VELTRA',
                'file_name' => 'logo.png',
                'file_extension' => 'png',
                'mime_type' => 'image/png',
                'file_size' => File::size($sourcePath),
                'width' => $width,
                'height' => $height,
                'sort_order' => 0,
                'is_cover' => true,
                'is_public' => true,
            ],
        );
    }
}
