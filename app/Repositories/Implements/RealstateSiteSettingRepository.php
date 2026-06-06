<?php

namespace App\Repositories\Implements;

use App\Models\RealstateSiteSetting;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Support\RealstateSiteTemplates;

class RealstateSiteSettingRepository implements IRealstateSiteSettingRepository
{
    public function current(): ?RealstateSiteSetting
    {
        return RealstateSiteSetting::query()->oldest()->first();
    }

    public function firstOrCreateDefault(): RealstateSiteSetting
    {
        return RealstateSiteSetting::query()->firstOrCreate([], [
            'template_set' => RealstateSiteTemplates::DEFAULT_TEMPLATE_SET,
            'theme' => RealstateSiteTemplates::DEFAULT_THEME,
            'pages' => RealstateSiteTemplates::defaultPages(),
        ]);
    }

    public function save(RealstateSiteSetting $setting, array $data): RealstateSiteSetting
    {
        $setting->fill($data);
        $setting->save();

        return $setting->refresh();
    }
}
