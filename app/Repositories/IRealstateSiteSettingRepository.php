<?php

namespace App\Repositories;

use App\Models\RealstateSiteSetting;

interface IRealstateSiteSettingRepository
{
    public function current(): ?RealstateSiteSetting;

    public function firstOrCreateDefault(): RealstateSiteSetting;

    public function save(RealstateSiteSetting $setting, array $data): RealstateSiteSetting;
}
