<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Image;
use App\Models\Lookup;
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

            Contact::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'email' => 'info@veltra.test',
                ],
                [
                    'phone' => '6013900000',
                    'mobile' => '3001234567',
                    'is_principal' => true,
                ],
            );

            Address::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'is_principal' => true,
                ],
                [
                    'address' => 'Calle 100 # 15-20',
                    'city_id' => $this->lookupId('city', 'Bogotá'),
                    'department_id' => $this->lookupId('department', 'Cundinamarca'),
                    'country_id' => $this->lookupId('country', 'Colombia'),
                    'zip_code' => '110111',
                    'sector' => 'Chicó',
                    'complement' => 'Oficina 501',
                ],
            );

            $this->seedLogo($company);
        });
    }

    private function lookupId(string $category, string $name): string
    {
        return Lookup::query()
            ->where('category', $category)
            ->where('name', $name)
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
