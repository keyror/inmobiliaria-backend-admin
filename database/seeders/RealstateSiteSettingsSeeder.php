<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\RealstateSiteSetting;
use App\Support\CacheKeys;
use App\Support\RealstateSiteTemplates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RealstateSiteSettingsSeeder extends Seeder
{
    // Copia la imagen de public/img a storage y crea/obtiene el registro en images
    private function img(string $fileName): string
    {
        $filePath = "images/{$fileName}";
        $disk = Storage::disk('public');
        $source = public_path("img/{$fileName}");

        if (! $disk->exists($filePath) && file_exists($source)) {
            $disk->put($filePath, file_get_contents($source));
        }

        $image = Image::query()->firstOrCreate(
            ['file_name' => $fileName],
            [
                'id' => Str::uuid(),
                'file_path' => $filePath,
                'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                'mime_type' => $disk->mimeType($filePath) ?? 'image/jpeg',
                'file_size' => $disk->size($filePath) ?? 0,
                'sort_order' => 0,
                'is_cover' => false,
                'is_public' => true,
            ]
        );

        return $image->url;
    }

    // Imágenes de Pixabay — libres de uso, sin atribución requerida
    private const string PX = 'https://cdn.pixabay.com/photo/';

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

        $banner = $this->img('banner-todas-secciones-sitio-publico.jpg');

        $pages['home']['content'] = [
            'background_image_url' => $this->img('fondo-pagina-inicio-sitio-publico-bg.jpg'),
            'featured_sections_bg_url' => $this->img('fondo-propiedades-destacadas-sitio-publico.jpg'),
            'hero_slides' => [
                [
                    'img' => self::PX.'2016/04/25/23/30/house-1353389_1280.jpg',
                    'link' => '/realstate/property',
                    'title' => 'Encuentra el hogar que siempre soñaste',
                    'description' => 'Casas y apartamentos seleccionados para comprar o arrendar en las mejores zonas',
                    'button_text' => 'Ver propiedades',
                ],
                [
                    'img' => self::PX.'2021/07/01/01/15/condominium-6377940_1280.jpg',
                    'link' => '/realstate/property',
                    'title' => 'Apartamentos modernos en conjuntos exclusivos',
                    'description' => 'Unidades con zonas comunes, seguridad y excelentes acabados',
                    'button_text' => 'Explorar apartamentos',
                ],
                [
                    'img' => self::PX.'2017/04/10/22/28/residence-2219972_1280.jpg',
                    'link' => '/realstate/contact',
                    'title' => 'Asesoría inmobiliaria personalizada',
                    'description' => 'Nuestro equipo te acompaña en cada paso del proceso de compra o arriendo',
                    'button_text' => 'Hablar con un asesor',
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
                            'name' => 'Asesoría',
                            'icon' => '/svg/icons.svg#key',
                            'path' => '/realstate/contact',
                        ],
                    ],
                ],
            ],
        ];

        $pages['propertyList']['content'] = [
            'banner_image_url' => $banner,
            'title' => 'Propiedades disponibles',
            'subtitle' => 'Encuentra casas, apartamentos y locales para comprar o arrendar. Filtra por ciudad, precio y tipo de inmueble.',
        ];

        $pages['propertyDetail']['content'] = [
            'contact_title' => '¿Te interesa esta propiedad?',
            'contact_description' => 'Déjanos tus datos y un asesor VELTRA se comunicará contigo para resolver dudas o coordinar una visita.',
            'show_related_properties' => true,
            'related_title' => 'Propiedades que también pueden interesarte',
            'gallery_fallback' => [
                self::PX.'2024/07/05/08/19/living-room-8874204_1280.jpg',
                self::PX.'2024/07/05/08/21/kitchen-8874296_1280.jpg',
                self::PX.'2024/07/05/08/21/bedroom-8874302_1280.jpg',
                self::PX.'2024/08/31/11/18/bathroom-9011240_1280.jpg',
                self::PX.'2024/08/31/11/18/dining-room-9011268_1280.jpg',
            ],
        ];

        $pages['about']['content'] = [
            'banner_image_url' => $banner,
            'intro' => [
                'title' => 'Especialistas en conectar personas con espacios',
                'description' => 'VELTRA es una inmobiliaria colombiana con más de 10 años de experiencia. Centralizamos propiedades, canales de contacto e información clave para que compradores, arrendatarios y propietarios avancen con confianza en cada proceso.',
                'images' => [
                    [
                        'url' => self::PX.'2021/01/09/15/30/house-5902664_1280.jpg',
                        'alt' => 'Casa residencial VELTRA',
                    ],
                    [
                        'url' => self::PX.'2024/07/15/11/54/apartment-8896495_1280.jpg',
                        'alt' => 'Apartamento moderno de lujo',
                    ],
                ],
            ],
            'history' => 'VELTRA nació en Bogotá con la misión de simplificar la experiencia inmobiliaria digital. A lo largo de los años hemos expandido nuestro portafolio a las principales ciudades del país, integrando inventario, información comercial y canales de atención en un sitio público claro y fácil de usar.',
            'mission' => 'Facilitar decisiones inmobiliarias mediante información organizada, respuesta oportuna y acompañamiento humano durante la búsqueda, venta o arriendo de inmuebles en Colombia.',
            'vision' => 'Consolidar una presencia pública confiable y reconocida en el sector inmobiliario colombiano, siendo la plataforma de referencia para compradores, arrendatarios y propietarios.',
            'why_choose_us' => [
                [
                    'icon' => 'fas fa-home',
                    'title' => 'Portafolio diverso',
                    'description' => 'Casas, apartamentos, locales y oficinas en las mejores zonas de las principales ciudades del país.',
                ],
                [
                    'icon' => 'fas fa-user-tie',
                    'title' => 'Asesores expertos',
                    'description' => 'Equipo comercial capacitado para acompañarte en cada etapa del proceso, desde la búsqueda hasta el cierre.',
                ],
                [
                    'icon' => 'fas fa-shield-alt',
                    'title' => 'Transacciones seguras',
                    'description' => 'Procesos legales claros y verificados para que compres o arriendas con total tranquilidad.',
                ],
                [
                    'icon' => 'fas fa-map-marker-alt',
                    'title' => 'Cobertura nacional',
                    'description' => 'Presencia en Bogotá, Medellín, Cali, Barranquilla y las principales ciudades intermedias.',
                ],
            ],
        ];

        $pages['services']['content'] = [
            'banner_image_url' => $banner,
            'hero' => [
                'title' => 'Servicios diseñados para tu proceso inmobiliario',
                'description' => 'Acompañamos la búsqueda, promoción y gestión de inmuebles con información clara, tecnología actualizada y contacto directo con asesores especializados.',
                'image' => self::PX.'2021/07/01/01/15/condominium-6377942_1280.jpg',
                'button_text' => 'Ver propiedades',
                'button_link' => '/realstate/property',
            ],
            'provided_services' => [
                [
                    'icon' => 'fas fa-house-user',
                    'title' => 'Acompañamiento en compra',
                    'description' => 'Te guiamos desde la búsqueda del inmueble ideal hasta la firma de escrituras, con soporte jurídico incluido.',
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-key',
                    'title' => 'Gestión de arriendo',
                    'description' => 'Publicamos tu inmueble, filtramos candidatos, elaboramos contratos y administramos cobros de canon.',
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-headset',
                    'title' => 'Atención comercial',
                    'description' => 'Centralizamos solicitudes y datos de contacto para facilitar la respuesta oportuna del equipo inmobiliario.',
                    'link' => '/realstate/contact',
                ],
                [
                    'icon' => 'fas fa-building',
                    'title' => 'Portafolio visible',
                    'description' => 'Presentamos tu inmueble con fotografías profesionales, plano y datos técnicos para atraer más interesados.',
                    'link' => '/realstate/property',
                ],
            ],
            'property_services' => [
                [
                    'icon' => 'fas fa-sign',
                    'title' => 'Venta de inmuebles',
                    'description' => 'Avalúo, publicación digital y acompañamiento comercial para cerrar la venta al mejor precio.',
                    'points' => ['Avalúo comercial gratuito', 'Fotografía profesional', 'Publicación multicanal', 'Cierre con escrituración'],
                    'link' => '/realstate/property',
                ],
                [
                    'icon' => 'fas fa-file-contract',
                    'title' => 'Arriendo residencial',
                    'description' => 'Selección de arrendatarios, estudio de crédito, contrato y póliza de arriendo incluidos.',
                    'points' => ['Estudio de crédito', 'Contrato certificado', 'Póliza de arriendo', 'Cobro de cánones'],
                    'link' => '/realstate/property',
                ],
                [
                    'icon' => 'fas fa-store',
                    'title' => 'Locales y oficinas',
                    'description' => 'Especialistas en inmuebles comerciales: locales, bodegas, oficinas y puntos de venta.',
                    'points' => ['Zonas de alto flujo', 'Análisis de rentabilidad', 'Negociación de condiciones', 'Trámites comerciales'],
                    'link' => '/realstate/property',
                ],
                [
                    'icon' => 'fas fa-chart-line',
                    'title' => 'Inversión inmobiliaria',
                    'description' => 'Identificamos oportunidades según tu perfil de inversión, presupuesto y objetivos de rentabilidad.',
                    'points' => ['Análisis de mercado', 'ROI proyectado', 'Comparación de opciones', 'Asesoría tributaria'],
                    'link' => '/realstate/contact',
                ],
            ],
        ];

        $pages['contact']['content'] = [
            'banner_image_url' => $banner,
            'title' => 'Hablemos de tu necesidad inmobiliaria',
            'description' => 'Cuéntanos qué estás buscando y un asesor VELTRA te contactará para orientarte sin compromiso.',
            'image' => self::PX.'2021/01/09/15/30/house-5902665_1280.jpg',
        ];

        $pages['layout']['content'] = [
            'footer_bg_url' => $this->img('footer-bg.jpg'),
        ];

        return $pages;
    }
}
