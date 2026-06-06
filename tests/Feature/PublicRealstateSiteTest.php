<?php

namespace Tests\Feature;

use App\Support\RealstateSiteTemplates;
use Tests\TestCase;

class PublicRealstateSiteTest extends TestCase
{
    public function test_realstate_template_set_keeps_all_pages_on_same_template(): void
    {
        $templates = RealstateSiteTemplates::templatesForSet('template2');

        $this->assertSame([
            'home' => 'template2',
            'propertyList' => 'template2',
            'propertyDetail' => 'template2',
            'about' => 'template2',
            'services' => 'template2',
            'contact' => 'template2',
        ], $templates);
    }

    public function test_realstate_site_pages_include_default_editable_content(): void
    {
        $pages = RealstateSiteTemplates::defaultPages();

        $this->assertSame([], $pages['home']['content']);
        $this->assertSame([], $pages['propertyList']['content']);
        $this->assertSame([], $pages['propertyDetail']['content']);
        $this->assertSame([
            'provided_services' => [],
            'property_services' => [],
        ], $pages['services']['content']);
        $this->assertSame([
            'history' => null,
            'mission' => null,
            'vision' => null,
            'why_choose_us' => [],
        ], $pages['about']['content']);
    }

    public function test_property_pages_are_editable_site_pages(): void
    {
        $this->assertTrue(RealstateSiteTemplates::isEditablePage('propertyList'));
        $this->assertTrue(RealstateSiteTemplates::isEditablePage('propertyDetail'));
    }
}
