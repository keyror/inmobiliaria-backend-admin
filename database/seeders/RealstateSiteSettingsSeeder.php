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
            'hero' => [
                'label' => 'VELTRA Real Estate',
                'title' => 'Encuentra espacios que se ajustan a tu forma de vivir',
                'subtitle' => 'Explora propiedades seleccionadas, compara ubicaciones y contacta al equipo comercial desde un solo lugar.',
                'button_text' => 'Ver propiedades',
                'button_link' => '/realstate/property',
                'images' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=80',
                        'alt' => 'Casa moderna con fachada iluminada',
                    ],
                    [
                        'url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=80',
                        'alt' => 'Interior moderno de apartamento',
                    ],
                ],
            ],
            'featured_sections' => [
                [
                    'title' => 'Propiedades listas para visitar',
                    'description' => 'Filtra por tipo, precio y ubicacion para encontrar opciones relevantes sin perder tiempo.',
                    'icon' => 'fas fa-search-location',
                ],
                [
                    'title' => 'Acompanamiento comercial',
                    'description' => 'Un equipo preparado para responder dudas, coordinar visitas y orientar cada paso.',
                    'icon' => 'fas fa-headset',
                ],
                [
                    'title' => 'Informacion clara',
                    'description' => 'Datos de contacto, ubicacion e imagenes organizadas para decidir con confianza.',
                    'icon' => 'fas fa-clipboard-check',
                ],
            ],
        ];

        $pages['propertyList']['content'] = [
            'title' => 'Propiedades disponibles',
            'subtitle' => 'Explora inmuebles para comprar o arrendar con filtros simples y datos claros.',
            'empty_state' => [
                'title' => 'No encontramos propiedades con esos filtros',
                'description' => 'Ajusta la busqueda o contactanos para recibir asesoria personalizada.',
            ],
            'banner' => [
                'image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1600&q=80',
                'alt' => 'Fachada de vivienda moderna',
            ],
            'highlights' => [
                'Filtros por tipo, ubicacion y precio',
                'Contacto directo con el equipo comercial',
                'Informacion organizada para comparar opciones',
            ],
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
            'detail_sections' => [
                [
                    'title' => 'Informacion verificada',
                    'description' => 'Revisa areas, ubicacion, precio y caracteristicas principales antes de solicitar contacto.',
                ],
                [
                    'title' => 'Agenda una visita',
                    'description' => 'Indica tu disponibilidad y el equipo comercial coordinara la mejor opcion.',
                ],
            ],
        ];

        $pages['about']['content'] = [
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
            'history' => [
                'title' => 'Construimos confianza desde cada contacto',
                'description' => 'VELTRA nace para simplificar la experiencia inmobiliaria digital, integrando inventario, informacion comercial y canales de atencion en un sitio publico claro y facil de usar.',
                'points' => [
                    'Atencion local y cercana',
                    'Procesos comerciales claros',
                    'Informacion disponible en todo momento',
                ],
            ],
            'mission' => [
                'title' => 'Conectar personas con inmuebles adecuados',
                'description' => 'Facilitar decisiones inmobiliarias mediante informacion organizada, respuesta oportuna y acompanamiento humano durante busqueda, venta o arriendo.',
                'points' => [
                    'Publicacion clara de propiedades',
                    'Contacto directo con asesores',
                    'Acompanamiento en cada etapa',
                ],
            ],
            'vision' => [
                'title' => 'Ser una referencia digital inmobiliaria',
                'description' => 'Consolidar una presencia publica confiable para que cada visitante encuentre propiedades, servicios y contacto en un solo lugar.',
                'points' => [
                    'Marca consistente',
                    'Experiencia simple',
                    'Crecimiento sostenible',
                ],
            ],
            'why_choose_us' => [
                [
                    'id' => 'organized-properties',
                    'icon' => 'fas fa-home',
                    'title' => 'Propiedades organizadas',
                    'description' => 'Presentamos informacion clara para que el usuario encuentre opciones relevantes y compare mejor.',
                    'points' => ['Filtros utiles', 'Datos visibles', 'Imagenes destacadas'],
                    'link' => '/realstate/property',
                ],
                [
                    'id' => 'direct-contact',
                    'icon' => 'fas fa-phone-alt',
                    'title' => 'Contacto directo',
                    'description' => 'Mostramos canales de comunicacion principales para reducir friccion entre usuario e inmobiliaria.',
                    'points' => ['Correo principal', 'Telefono visible', 'Formulario publico'],
                    'link' => '/realstate/contact',
                ],
                [
                    'id' => 'local-coverage',
                    'icon' => 'fas fa-map-marker-alt',
                    'title' => 'Cobertura local',
                    'description' => 'La informacion de sede y ubicacion ayuda a generar confianza en cada consulta.',
                    'points' => ['Sede principal', 'Ubicacion clara', 'Asesoria cercana'],
                ],
            ],
        ];

        $pages['services']['content'] = [
            'hero' => [
                'title' => 'Servicios pensados para tu proceso inmobiliario',
                'description' => 'Acompanamos busqueda, promocion y gestion de inmuebles con informacion clara y contacto directo.',
                'image' => 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=1600&q=80',
                'button_text' => 'Ver propiedades',
                'button_link' => '/realstate/property',
            ],
            'provided_services' => [
                [
                    'id' => 'advisory',
                    'icon' => 'fas fa-house-user',
                    'title' => 'Acompanamiento inmobiliario',
                    'description' => 'Orientamos al visitante para encontrar opciones, resolver dudas y avanzar con informacion clara.',
                    'points' => ['Analisis de necesidad', 'Opciones comparables', 'Contacto oportuno'],
                    'link' => '/realstate/contact',
                ],
                [
                    'id' => 'commercial-attention',
                    'icon' => 'fas fa-headset',
                    'title' => 'Atencion comercial',
                    'description' => 'Centralizamos solicitudes y datos de contacto para facilitar la respuesta del equipo inmobiliario.',
                    'points' => ['Respuesta por correo', 'Gestion de solicitudes', 'Seguimiento inicial'],
                    'link' => '/realstate/contact',
                ],
                [
                    'id' => 'portfolio',
                    'icon' => 'fas fa-building',
                    'title' => 'Portafolio visible',
                    'description' => 'Presentamos inmuebles, ubicaciones y detalles clave para que el usuario compare mejor.',
                    'points' => ['Listado publico', 'Detalle de propiedad', 'Imagenes y ubicacion'],
                    'link' => '/realstate/property',
                ],
            ],
            'property_services' => [
                [
                    'id' => 'sell',
                    'icon' => 'fas fa-sign',
                    'title' => 'Venta de inmuebles',
                    'description' => 'Acompanamos la publicacion y contacto de propiedades disponibles para venta.',
                    'points' => ['Ficha publica clara', 'Contacto directo', 'Seguimiento comercial'],
                    'link' => '/realstate/property',
                ],
                [
                    'id' => 'rent',
                    'icon' => 'fas fa-key',
                    'title' => 'Arriendo',
                    'description' => 'Presentamos inmuebles en arriendo con informacion organizada para el visitante.',
                    'points' => ['Filtros de busqueda', 'Datos de ubicacion', 'Solicitud rapida'],
                    'link' => '/realstate/property',
                ],
                [
                    'id' => 'management',
                    'icon' => 'fas fa-clipboard-list',
                    'title' => 'Gestion de propiedad',
                    'description' => 'Centralizamos datos, contactos e imagenes para mejorar la promocion digital.',
                    'points' => ['Inventario publico', 'Marca del tenant', 'Canales visibles'],
                    'link' => '/realstate/contact',
                ],
                [
                    'id' => 'investment',
                    'icon' => 'fas fa-chart-line',
                    'title' => 'Asesoria para inversion',
                    'description' => 'Ayudamos a identificar oportunidades segun ubicacion, presupuesto y objetivo de uso.',
                    'points' => ['Perfil de busqueda', 'Comparacion de opciones', 'Acompanamiento comercial'],
                    'link' => '/realstate/contact',
                ],
            ],
        ];

        $pages['contact']['content'] = [
            'title' => 'Hablemos de tu necesidad inmobiliaria',
            'description' => 'Escribenos y el equipo de VELTRA revisara tu solicitud por el canal configurado.',
            'image' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1400&q=80',
            'form_title' => 'Solicita informacion',
            'form_description' => 'Cuentanos que tipo de inmueble buscas o que propiedad quieres promocionar.',
            'success_message' => 'Mensaje enviado exitosamente.',
            'contact_cards_title' => 'Canales de contacto',
        ];

        return $pages;
    }
}
