# Realstate Site Content Schema

Este documento describe la estructura usada por `realstate_site_settings.pages.*`.
Solo deben existir campos que se pintan en el sitio publico y se administran desde el admin.

## Estructura de página

Cada pagina guarda estado y contenido:

| Campo | Tipo | Uso |
| --- | --- | --- |
| `is_active` | `boolean` | Si es `false`, el sitio publico responde la pagina como no disponible. |
| `template` | `template1\|template2` | Se hereda del `template_set` global. |
| `content` | `object` | Contenido editable de la pagina. |

## Tipos base

### EditableImage

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `url` | `string|null` | `https://.../house.jpg` |
| `alt` | `string|null` | `Casa moderna` |

### EditableCard

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `icon` | `string|null` | `fas fa-home` |
| `title` | `string` | `Asesoria inmobiliaria` |
| `description` | `string` | `Acompanamiento en compra y arriendo.` |
| `link` | `string|null` | `/realstate/contact` |

### EditableCardWithPoints

Extiende `EditableCard`.

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `points` | `string[]` | `["Contacto directo"]` |

## Home

| Campo | Tipo | Uso |
| --- | --- | --- |
| `background_image_url` | `string|null` | Fondo visual de la pagina de inicio. |
| `hero_slides` | `HomeSlide[]` | Slides del hero principal. |
| `featured_sections` | `FeaturedSectionGroup[]` | Grupos de servicios destacados bajo el hero. |

### HomeSlide

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `img` | `string|null` | `https://.../slide.jpg` |
| `link` | `string|null` | `/realstate/property` |
| `title` | `string` | `Encuentra tu proximo inmueble` |
| `description` | `string|null` | `Propiedades seleccionadas` |
| `button_text` | `string|null` | `Ver propiedades` |

### FeaturedSectionGroup

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `heading` | `string` | `Servicios destacados` |
| `type` | `string` | `filter` |
| `icons` | `FeaturedSectionIcon[]` | Ver estructura abajo. |

### FeaturedSectionIcon

| Campo | Tipo | Ejemplo |
| --- | --- | --- |
| `name` | `string` | `Propiedades` |
| `icon` | `string` | `/svg/icons.svg#home-lock` |
| `path` | `string` | `/realstate/property` |

## Property List

| Campo | Tipo | Uso |
| --- | --- | --- |
| `banner_image_url` | `string|null` | Imagen del breadcrumb/banner. |
| `title` | `string|null` | Titulo del listado. |
| `subtitle` | `string|null` | Subtitulo del listado. |

## Property Detail

No usa banner generico. El encabezado visual es la galeria/slider de imagenes de la propiedad.

| Campo | Tipo | Uso |
| --- | --- | --- |
| `contact_title` | `string|null` | Titulo del formulario/contacto del inmueble. |
| `contact_description` | `string|null` | Descripcion del contacto del inmueble. |
| `show_related_properties` | `boolean` | Activa el bloque de propiedades relacionadas. |
| `related_title` | `string|null` | Titulo del bloque de relacionadas. |
| `gallery_fallback` | `string[]` | Imagenes fallback si la propiedad no tiene galeria. |

## About

| Campo | Tipo | Uso |
| --- | --- | --- |
| `banner_image_url` | `string|null` | Imagen del breadcrumb/banner. |
| `intro.title` | `string|null` | Titulo introductorio. |
| `intro.description` | `string|null` | Descripcion introductoria. |
| `intro.images` | `EditableImage[]` | Imagenes de la seccion. |
| `history` | `string|null` | Historia. |
| `mission` | `string|null` | Mision. |
| `vision` | `string|null` | Vision. |
| `why_choose_us` | `FeaturedSection[]` | Razones para elegir la empresa. |

## Services

| Campo | Tipo | Uso |
| --- | --- | --- |
| `banner_image_url` | `string|null` | Imagen del breadcrumb/banner. |
| `hero.title` | `string|null` | Titulo del hero de servicios. |
| `hero.description` | `string|null` | Descripcion del hero. |
| `hero.image` | `string|null` | Imagen principal. |
| `hero.button_text` | `string|null` | Texto del boton. |
| `hero.button_link` | `string|null` | URL del boton. |
| `provided_services` | `EditableCard[]` | Servicios generales. |
| `property_services` | `EditableCardWithPoints[]` | Servicios de propiedades. |

## Contact

| Campo | Tipo | Uso |
| --- | --- | --- |
| `banner_image_url` | `string|null` | Imagen del breadcrumb/banner. |
| `title` | `string|null` | Titulo principal. |
| `description` | `string|null` | Texto descriptivo. |
| `image` | `string|null` | Imagen de la seccion. |
| `form_title` | `string|null` | Titulo del formulario. |
| `form_description` | `string|null` | Descripcion del formulario. |
| `success_message` | `string|null` | Mensaje despues del envio. |
| `contact_cards_title` | `string|null` | Titulo de los canales de contacto. |
