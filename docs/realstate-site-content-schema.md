# RealstateSiteSetting — Schema

Documenta el modelo `RealstateSiteSetting` (tabla `realstate_site_settings`) y la estructura JSON del campo `pages`.

---

## Columnas del modelo

| Columna | Tipo | Descripción |
|---|---|---|
| `template_set` | string | Plantilla activa del sitio (ej. `"template1"`). Constantes en `RealstateSiteTemplates`. |
| `theme` | JSON | Colores del tema: `{"primary": "#f35d43", "secondary": "#f34451", "accent": "#f35d43"}` |
| `pages` | JSON | Contenido editable de cada página pública (ver sección siguiente) |
| `backup_template_set` | string\|null | Copia de seguridad de `template_set` antes del último cambio |
| `backup_theme` | JSON\|null | Copia de seguridad de `theme` |
| `backup_pages` | JSON\|null | Copia de seguridad de `pages` |

Los campos `backup_*` permiten revertir el sitio al estado anterior si se descarta un cambio.

**Auditoría**: solo registra cambios en `template_set` y `theme`. El campo `pages` no se audita (demasiado voluminoso).

---

## Estructura de `pages`

Cada clave de `pages` corresponde a una sección del sitio público. El frontend admin escribe y lee esta estructura; el frontend público la consume.

### Estructura por página

```json
{
  "home": { ... },
  "property_list": { ... },
  "property_detail": { ... },
  "about": { ... },
  "services": { ... },
  "contact": { ... }
}
```

---

## Tipos base reutilizables

### EditableImage

| Campo | Tipo | Ejemplo |
|---|---|---|
| `url` | `string\|null` | `"https://.../house.jpg"` |
| `alt` | `string\|null` | `"Casa moderna"` |

### EditableCard

| Campo | Tipo | Ejemplo |
|---|---|---|
| `icon` | `string\|null` | `"fas fa-home"` |
| `title` | `string` | `"Asesoría inmobiliaria"` |
| `description` | `string` | `"Acompañamiento en compra y arriendo."` |
| `link` | `string\|null` | `"/realstate/contact"` |

### EditableCardWithPoints

Extiende `EditableCard`.

| Campo | Tipo |
|---|---|
| `points` | `string[]` |

---

## Home

| Campo | Tipo | Descripción |
|---|---|---|
| `background_image_url` | `string\|null` | Fondo visual de la página de inicio |
| `hero_slides` | `HomeSlide[]` | Slides del hero principal |
| `featured_sections` | `FeaturedSectionGroup[]` | Grupos de servicios bajo el hero |
| `brands` | `EditableImage[]` | Logos de marcas/aliados, slider antes del footer |

### HomeSlide

| Campo | Tipo |
|---|---|
| `img` | `string\|null` |
| `link` | `string\|null` |
| `title` | `string` |
| `description` | `string\|null` |
| `button_text` | `string\|null` |

### FeaturedSectionGroup

| Campo | Tipo | Ejemplo |
|---|---|---|
| `heading` | `string` | `"Servicios destacados"` |
| `type` | `string` | `"filter"` |
| `icons` | `FeaturedSectionIcon[]` | |

### FeaturedSectionIcon

| Campo | Tipo | Ejemplo |
|---|---|---|
| `name` | `string` | `"Propiedades"` |
| `icon` | `string` | `"/svg/icons.svg#home-lock"` |
| `path` | `string` | `"/realstate/property"` |

---

## Property List

| Campo | Tipo | Descripción |
|---|---|---|
| `banner_image_url` | `string\|null` | Imagen del breadcrumb/banner |
| `title` | `string\|null` | Título del listado |
| `subtitle` | `string\|null` | Subtítulo del listado |

---

## Property Detail

El encabezado visual es la galería de imágenes de la propiedad, no un banner genérico.

| Campo | Tipo | Descripción |
|---|---|---|
| `contact_title` | `string\|null` | Título del formulario de contacto |
| `contact_description` | `string\|null` | Descripción del formulario |
| `show_related_properties` | `boolean` | Activa el bloque de propiedades relacionadas |
| `related_title` | `string\|null` | Título del bloque de relacionadas |
| `gallery_fallback` | `string[]` | Imágenes fallback si la propiedad no tiene galería |

---

## About

| Campo | Tipo | Descripción |
|---|---|---|
| `banner_image_url` | `string\|null` | Imagen del banner |
| `intro.title` | `string\|null` | Título introductorio |
| `intro.description` | `string\|null` | Descripción introductoria |
| `intro.images` | `EditableImage[]` | Imágenes de la sección |
| `history` | `string\|null` | Historia de la empresa |
| `mission` | `string\|null` | Misión |
| `vision` | `string\|null` | Visión |
| `why_choose_us` | `EditableCard[]` | Razones para elegir la empresa |

---

## Services

| Campo | Tipo | Descripción |
|---|---|---|
| `banner_image_url` | `string\|null` | Imagen del banner |
| `hero.title` | `string\|null` | Título del hero |
| `hero.description` | `string\|null` | Descripción del hero |
| `hero.image` | `string\|null` | Imagen principal |
| `hero.button_text` | `string\|null` | Texto del botón |
| `hero.button_link` | `string\|null` | URL del botón |
| `provided_services` | `EditableCard[]` | Servicios generales |
| `property_services` | `EditableCardWithPoints[]` | Servicios de propiedades con puntos |

---

## Contact

| Campo | Tipo | Descripción |
|---|---|---|
| `banner_image_url` | `string\|null` | Imagen del banner |
| `title` | `string\|null` | Título principal |
| `description` | `string\|null` | Texto descriptivo |
| `image` | `string\|null` | Imagen de la sección |

---

## Layout

| Campo | Tipo | Descripción |
|---|---|---|
| `footer_logo_url` | `string\|null` | Logo que se muestra en el footer del sitio. Fallback al logo principal de la empresa si está vacío |
| `footer_bg_url` | `string\|null` | Imagen de fondo del footer (global) |
| `favicon_url` | `string\|null` | Favicon del sitio (tab del browser). Fallback al logo de la empresa si está vacío |

### Uso del favicon en los frontends

- **Frontend público** (`frontend-public/`): `app.vue` lee `sitePages.layout.favicon_url` del store. Fallback al `logoUrl`.
- **Frontend admin** (`frontend/`): `favicon_url` se incluye en la respuesta de `/api/public/company` (dentro del cache `public_company`). Se expone como `faviconUrl` computed en `store/publicCompany.ts`. `app.vue` lo aplica con `useHead`.
- **Invalidación de cache**: `RealstateTemplateManagementService` borra `CacheKeys::publicCompany()` y `CacheKeys::publicRealstateSite()` cada vez que se guardan cambios en el sitio (página, template, restore).
