<?php

namespace Database\Seeders;

use App\Models\RealstateSiteSetting;
use App\Support\CacheKeys;
use App\Support\RealstateSiteTemplates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class RealstateSiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        RealstateSiteSetting::query()->updateOrCreate([], [
            'template_set' => RealstateSiteTemplates::DEFAULT_TEMPLATE_SET,
            'theme' => [
                'primary' => '#46b5d1',
                'secondary' => '#1f2937',
                'accent' => '#f35d43',
            ],
            'pages' => $this->pages(),
        ]);

        Cache::forget(CacheKeys::publicRealstateSite());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function pages(): array
    {
        $pages = RealstateSiteTemplates::defaultPages();

        $pages['home']['content'] = [
            'background_image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1800&q=80',
            'featured_sections_bg_url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1800&q=80',
            'hero_slides' => [
                [
                    'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=80',
                    'link' => '/realstate/property',
                    'title' => 'Encuentra espacios que se ajustan a tu forma de vivir',
                    'description' => 'Propiedades seleccionadas para comprar o arrendar',
                    'button_text' => 'Ver propiedades',
                ],
                [
                    'img' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=80',
                    'link' => '/realstate/contact',
                    'title' => 'Recibe acompañamiento inmobiliario personalizado',
                    'description' => 'Agenda una visita o resuelve tus dudas con el equipo comercial',
                    'button_text' => 'Contactar asesor',
                ],
            ],
            'featured_sections' => [
                [
                    'heading' => 'Servicios destacados',
                    'type' => 'filter',
                    'icons' => [
                        [
                            'name' => 'Propiedades',
                            'icon' => '/svg/icons.svg#home-lock',
                            'path' => '/realstate/property',
                        ],
                        [
                            'name' => 'Favoritos',
                            'icon' => '/svg/icons.svg#home-heart',
                            'path' => '/realstate/property',
                        ],
                        [
                            'name' => 'Asesoria',
                            'icon' => '/svg/icons.svg#key',
                            'path' => '/realstate/contact',
                        ],
                    ],
                ],
            ],
        ];

        $pages['propertyList']['content'] = [
            'banner_image_url' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1600&q=80',
            'title' => 'Propiedades disponibles',
            'subtitle' => 'Explora inmuebles para comprar o arrendar con filtros simples y datos claros.',
        ];

        $pages['propertyDetail']['content'] = [
            'contact_title' => '¿Te interesa esta propiedad?',
            'contact_description' => 'Envia tus datos y el equipo de VELTRA te contactara para resolver dudas o coordinar una visita.',
            'show_related_properties' => true,
            'related_title' => 'Propiedades que tambien pueden interesarte',
            'gallery_fallback' => [
                'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1600607687644-c7171b42498f?auto=format&fit=crop&w=1600&q=80',
            ],
        ];

        $pages['about']['content'] = [
            'banner_image_url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1600&q=80',
            'intro' => [
                'title' => 'Especialistas en conectar personas con espacios',
                'description' => 'VELTRA centraliza propiedades, canales de contacto e informacion clave para que compradores, arrendatarios y propietarios avancen con confianza.',
                'images' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
                        'alt' => 'Agente inmobiliario entregando llaves',
                    ],
                    [
                        'url' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1200&q=80',
                        'alt' => 'Sala moderna de una propiedad',
                    ],
                ],
            ],
            'history' => 'VELTRA nace para simplificar la experiencia inmobiliaria digital, integrando inventario, informacion comercial y canales de atencion en un sitio publico claro y facil de usar.',
            'mission' => 'Facilitar decisiones inmobiliarias mediante informacion organizada, respuesta oportuna y acompanamiento humano durante busqueda, venta o arriendo.',
            'vision' => 'Consolidar una presencia publica confiable para que cada visitante encuentre propiedades, servicios y contacto en un solo lugar.',
            'why_choose_us' => [
                [
                    'icon' => 'fas fa-home',
                    'title' => 'Propiedades organizadas',
                    'description' => 'Presentamos informacion clara para que el usuario encuentre opciones relevantes y compare mejor.',
                ],
                [
                    'icon' => 'fas fa-phone-alt',
                    'title' => 'Contacto directo',
                    'description' => 'Mostramos canales de comunicacion principales para reducir friccion entre usuario e inmobiliaria.',
                ],
                [
                    'icon' => 'fas fa-map-marker-alt',
                    'title' => 'Cobertura local',
                    'description' => 'La informacion de sede y ubicacion ayuda a generar confianza en cada consulta.',
                ],
            ],
        ];

        $pages['services']['content'] = [
            'banner_image_url' => 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=1600&q=80',
            'hero' => [
                'title' => 'Servicios pensados para tu proceso inmobiliario',
                'description' => 'Acompanamos busqueda, promocion y gestion de inmuebles con informacion clara y contacto directo.',
                'image' => 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=1600&q=80',
                'button_text' => 'Ver propiedades',
                'button_link' => '/realstate/property',
            ],
            'provided_services' => [
                [
                    'icon' => 'fas fa-house-user',
                    'title' => 'Acompanamiento inmobiliario',
                    'description' => 'Orientamos al visitante para encontrar opciones, resolver dudas y avanzar con informacion clara.',
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-headset',
                    'title' => 'Atencion comercial',
                    'description' => 'Centralizamos solicitudes y datos de contacto para facilitar la respuesta del equipo inmobiliario.',
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-building',
                    'title' => 'Portafolio visible',
                    'description' => 'Presentamos inmuebles, ubicaciones y detalles clave para que el usuario compare mejor.',
                    'link' => '/realstate/property',
                ],
            ],
            'property_services' => [
                [
                    'icon' => 'fas fa-sign',
                    'title' => 'Venta de inmuebles',
                    'description' => 'Acompanamos la publicacion y contacto de propiedades disponibles para venta.',
                    'points' => ['Ficha publica clara', 'Contacto directo', 'Seguimiento comercial'],
                    'link' => '/realstate/property',
                ],
                [
                    'icon' => 'fas fa-key',
                    'title' => 'Arriendo',
                    'description' => 'Presentamos inmuebles en arriendo con informacion organizada para el visitante.',
                    'points' => ['Filtros de busqueda', 'Datos de ubicacion', 'Solicitud rapida'],
                    'link' => '/realstate/property',
                ],
                [
                    'icon' => 'fas fa-clipboard-list',
                    'title' => 'Gestion de propiedad',
                    'description' => 'Centralizamos datos, contactos e imagenes para mejorar la promocion digital.',
                    'points' => ['Inventario publico', 'Marca del tenant', 'Canales visibles'],
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-chart-line',
                    'title' => 'Asesoria para inversion',
                    'description' => 'Ayudamos a identificar oportunidades segun ubicacion, presupuesto y objetivo de uso.',
                    'points' => ['Perfil de busqueda', 'Comparacion de opciones', 'Acompanamiento comercial'],
                    'link' => '/realstate/contact',
                ],
            ],
        ];

        $pages['contact']['content'] = [
            'banner_image_url' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1600&q=80',
            'title' => 'Hablemos de tu necesidad inmobiliaria',
            'description' => 'Escribenos y el equipo de VELTRA revisara tu solicitud por el canal configurado.',
            'image' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1400&q=80',
        ];

        $pages['layout']['content'] = [
            'footer_bg_url' => 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?auto=format&fit=crop&w=1800&q=80',
        ];

        return $pages;
    }
}
