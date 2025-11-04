<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class LookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lookups = [
            // STATUS DE PROPIEDADES
            ['category' => 'property_status', 'name' => 'Nuevo', 'alias' => 'nuevo'],
            ['category' => 'property_status', 'name' => 'Remodelado', 'alias' => 'remodelado'],
            ['category' => 'property_status', 'name' => 'Usado', 'alias' => 'usado'],
            ['category' => 'property_status', 'name' => 'En construcción', 'alias' => 'en_construccion'],
            ['category' => 'property_status', 'name' => 'Para remodelar', 'alias' => 'para_remodelar'],

            // TIPOS DE OFERTA
            ['category' => 'offer_type', 'name' => 'Venta', 'alias' => 'venta'],
            ['category' => 'offer_type', 'name' => 'Arriendo', 'alias' => 'arriendo'],
            ['category' => 'offer_type', 'name' => 'Transferencia', 'alias' => 'transferencia'],
            ['category' => 'offer_type', 'name' => 'Venta y Arriendo', 'alias' => 'venta_y_arriendo'],
            ['category' => 'offer_type', 'name' => 'Hipoteca', 'alias' => 'hipoteca'],

            // TIPOS DE PROPIEDAD
            ['category' => 'property_type', 'name' => 'Apartamento', 'alias' => 'apartamento'],
            ['category' => 'property_type', 'name' => 'Casa', 'alias' => 'casa'],
            ['category' => 'property_type', 'name' => 'Bodega', 'alias' => 'bodega'],
            ['category' => 'property_type', 'name' => 'Local', 'alias' => 'local'],
            ['category' => 'property_type', 'name' => 'Oficina', 'alias' => 'oficina'],
            ['category' => 'property_type', 'name' => 'Lote', 'alias' => 'lote'],
            ['category' => 'property_type', 'name' => 'Finca', 'alias' => 'finca'],

            // TIPOS DE GARAJE
            ['category' => 'garage_type', 'name' => 'Garaje cubierto', 'alias' => 'garaje_cubierto'],
            ['category' => 'garage_type', 'name' => 'Garaje descubierto', 'alias' => 'garaje_descubierto'],
            ['category' => 'garage_type', 'name' => 'Garaje subterráneo', 'alias' => 'garaje_subterraneo'],
            ['category' => 'garage_type', 'name' => 'Garaje en paralela', 'alias' => 'garaje_paralela'],

            // TIPOS DE PARQUEADERO
            ['category' => 'parking_type', 'name' => 'Parqueadero cubierto', 'alias' => 'parqueadero_cubierto'],
            ['category' => 'parking_type', 'name' => 'Parqueadero descubierto', 'alias' => 'parqueadero_descubierto'],
            ['category' => 'parking_type', 'name' => 'Parqueadero visitantes', 'alias' => 'parqueadero_visitantes'],
            ['category' => 'parking_type', 'name' => 'Parqueadero moto', 'alias' => 'parqueadero_moto'],

            // TIPOS DE ÁREA
            ['category' => 'area_type', 'name' => 'Área total', 'alias' => 'area_total'],
            ['category' => 'area_type', 'name' => 'Área construida', 'alias' => 'area_construida'],
            ['category' => 'area_type', 'name' => 'Área del lote', 'alias' => 'area_lote'],
            ['category' => 'area_type', 'name' => 'Área privada', 'alias' => 'area_privada'],
            ['category' => 'area_type', 'name' => 'Área jardín', 'alias' => 'area_jardin'],
            ['category' => 'area_type', 'name' => 'Área terraza', 'alias' => 'area_terraza'],
            ['category' => 'area_type', 'name' => 'Área balcón', 'alias' => 'area_balcon'],

            // UNIDADES DE ÁREA
            ['category' => 'area_unit', 'name' => 'Metros cuadrados', 'alias' => 'metros_cuadrados'],
            ['category' => 'area_unit', 'name' => 'Pies cuadrados', 'alias' => 'pies_cuadrados'],
            ['category' => 'area_unit', 'name' => 'Hectáreas', 'alias' => 'hectareas'],
            ['category' => 'area_unit', 'name' => 'Yardas', 'alias' => 'yardas'],

            // TIPOS DE PRECIO
            ['category' => 'price_type', 'name' => 'Precio de venta', 'alias' => 'precio_venta'],
            ['category' => 'price_type', 'name' => 'Precio de arriendo', 'alias' => 'precio_arriendo'],
            ['category' => 'price_type', 'name' => 'Precio negociable', 'alias' => 'precio_negociable'],

            // CANALES DE PUBLICACIÓN
            ['category' => 'publish_channel', 'name' => 'Sitio web', 'alias' => 'sitio_web'],
            ['category' => 'publish_channel', 'name' => 'Ciencuadras', 'alias' => 'ciencuadras'],
            ['category' => 'publish_channel', 'name' => 'Metrocuadrado', 'alias' => 'metrocuadrado'],
            ['category' => 'publish_channel', 'name' => 'Fincaraíz', 'alias' => 'fincaraiz'],
            ['category' => 'publish_channel', 'name' => 'Facebook', 'alias' => 'facebook'],
            ['category' => 'publish_channel', 'name' => 'WhatsApp', 'alias' => 'whatsapp'],

            // CARACTERÍSTICAS/FEATURES
            ['category' => 'feature', 'name' => 'Aire acondicionado', 'alias' => 'aire_acondicionado'],
            ['category' => 'feature', 'name' => 'Internet/WiFi', 'alias' => 'internet'],
            ['category' => 'feature', 'name' => 'Terraza', 'alias' => 'terraza'],
            ['category' => 'feature', 'name' => 'Kiosco', 'alias' => 'kiosco'],
            ['category' => 'feature', 'name' => 'Chimenea', 'alias' => 'chimenea'],
            ['category' => 'feature', 'name' => 'Balcón', 'alias' => 'balcon'],
            ['category' => 'feature', 'name' => 'Ventanal', 'alias' => 'ventanal'],
            ['category' => 'feature', 'name' => 'Piscina', 'alias' => 'piscina'],
            ['category' => 'feature', 'name' => 'Jardín', 'alias' => 'jardin'],
            ['category' => 'feature', 'name' => 'BBQ', 'alias' => 'bbq'],
            ['category' => 'feature', 'name' => 'Gimnasio', 'alias' => 'gimnasio'],
            ['category' => 'feature', 'name' => 'Portería 24h', 'alias' => 'porteria_24h'],
            ['category' => 'feature', 'name' => 'Ascensor', 'alias' => 'ascensor'],
            ['category' => 'feature', 'name' => 'Salón comunal', 'alias' => 'salon_comunal'],
            ['category' => 'feature', 'name' => 'Zona de juegos', 'alias' => 'zona_juegos'],
            ['category' => 'feature', 'name' => 'Zona verde', 'alias' => 'zona_verde'],
            ['category' => 'feature', 'name' => 'Cuarto útil', 'alias' => 'cuarto_util'],
            ['category' => 'feature', 'name' => 'Walk-in closet', 'alias' => 'walk_in_closet'],
            ['category' => 'feature', 'name' => 'Estudio', 'alias' => 'estudio'],
            ['category' => 'feature', 'name' => 'Amoblado', 'alias' => 'amoblado'],

            // TIPOS DE OBLIGACIONES
            ['category' => 'obligation_type', 'name' => 'Impuesto predial', 'alias' => 'impuesto_predial'],
            ['category' => 'obligation_type', 'name' => 'Hipoteca', 'alias' => 'hipoteca'],
            ['category' => 'obligation_type', 'name' => 'Mantenimiento', 'alias' => 'mantenimiento'],
            ['category' => 'obligation_type', 'name' => 'Reparaciones', 'alias' => 'reparaciones'],
            ['category' => 'obligation_type', 'name' => 'Seguro', 'alias' => 'seguro'],
            ['category' => 'obligation_type', 'name' => 'Servicios públicos', 'alias' => 'servicios_publicos'],
            ['category' => 'obligation_type', 'name' => 'Administración', 'alias' => 'administracion'],

            // FRECUENCIAS
            ['category' => 'frequency', 'name' => 'Mensual', 'alias' => 'mensual'],
            ['category' => 'frequency', 'name' => 'Bimestral', 'alias' => 'bimestral'],
            ['category' => 'frequency', 'name' => 'Trimestral', 'alias' => 'trimestral'],
            ['category' => 'frequency', 'name' => 'Semestral', 'alias' => 'semestral'],
            ['category' => 'frequency', 'name' => 'Anual', 'alias' => 'anual'],
            ['category' => 'frequency', 'name' => 'Una sola vez', 'alias' => 'una_vez'],

            // TIPOS DE DOCUMENTOS
            ['category' => 'document_type', 'name' => 'Cédula de Ciudadanía', 'alias' => 'CC'],
            ['category' => 'document_type', 'name' => 'Tarjeta de Identidad', 'alias' => 'TI'],
            ['category' => 'document_type', 'name' => 'Registro Civil', 'alias' => 'RC'],
            ['category' => 'document_type', 'name' => 'Cédula de Extranjería', 'alias' => 'CE'],
            ['category' => 'document_type', 'name' => 'Pasaporte', 'alias' => 'PP'],
            ['category' => 'document_type', 'name' => 'Número de Identificación Tributaria', 'alias' => 'NIT'],

            // ESTADOS DE DOCUMENTOS
            ['category' => 'document_status', 'name' => 'Vigente', 'alias' => 'vigente'],
            ['category' => 'document_status', 'name' => 'Vencido', 'alias' => 'vencido'],
            ['category' => 'document_status', 'name' => 'Pendiente', 'alias' => 'pendiente'],
            ['category' => 'document_status', 'name' => 'Aprobado', 'alias' => 'aprobado'],
            ['category' => 'document_status', 'name' => 'Rechazado', 'alias' => 'rechazado'],

            // TIPOS DE IMÁGENES
            ['category' => 'image_type', 'name' => 'Foto Principal', 'alias' => 'foto_principal'],
            ['category' => 'image_type', 'name' => 'Fachada', 'alias' => 'fachada'],
            ['category' => 'image_type', 'name' => 'Interior', 'alias' => 'interior'],
            ['category' => 'image_type', 'name' => 'Sala', 'alias' => 'sala'],
            ['category' => 'image_type', 'name' => 'Cocina', 'alias' => 'cocina'],
            ['category' => 'image_type', 'name' => 'Habitación', 'alias' => 'habitacion'],
            ['category' => 'image_type', 'name' => 'Baño', 'alias' => 'bano'],
            ['category' => 'image_type', 'name' => 'Terraza', 'alias' => 'terraza_foto'],
            ['category' => 'image_type', 'name' => 'Garaje', 'alias' => 'garaje_foto'],
            ['category' => 'image_type', 'name' => 'Zona Común', 'alias' => 'zona_comun'],
            ['category' => 'image_type', 'name' => 'Plano', 'alias' => 'plano'],

            // TIPOS DE PERSONA
            ['category' => 'organization_type', 'name' => 'Persona Natural', 'alias' => 'PN'],
            ['category' => 'organization_type', 'name' => 'Persona Jurídica', 'alias' => 'PJ'],

            // Para la inmobiliaria (empresa)
            ['category' => 'tax_responsibility', 'name' => 'Responsable de IVA', 'alias' => 'O-15', 'description' => 'Responsable del régimen común del IVA'],
            ['category' => 'tax_responsibility', 'name' => 'Agente de Retención en la Fuente', 'alias' => 'O-07', 'description' => 'Obligado a practicar retención en la fuente'],
            ['category' => 'tax_responsibility', 'name' => 'Agente de Retención IVA', 'alias' => 'O-14', 'description' => 'Obligado a practicar retención en la fuente del IVA'],
            ['category' => 'tax_responsibility', 'name' => 'Obligado Factura Electrónica', 'alias' => 'O-42', 'description' => 'Obligado a expedir factura electrónica de venta'],
            ['category' => 'tax_responsibility', 'name' => 'Obligado a Llevar Contabilidad', 'alias' => 'O-26', 'description' => 'Obligado a llevar contabilidad según normativa'],
            ['category' => 'tax_responsibility', 'name' => 'Informante de Exógena', 'alias' => 'O-23', 'description' => 'Obligado a presentar información exógena'],
            ['category' => 'tax_responsibility', 'name' => 'Agente de Retención de ICA', 'alias' => 'O-32', 'description' => 'Agente de retención del impuesto de industria y comercio'],

            // Para propietarios/arrendadores (personas naturales)
            ['category' => 'tax_responsibility', 'name' => 'No Responsable de IVA', 'alias' => 'R-99-PN', 'description' => 'No responsable del impuesto sobre las ventas - Persona Natural'],
            ['category' => 'tax_responsibility', 'name' => 'Declarante de Renta', 'alias' => 'O-38', 'description' => 'Obligado a declarar renta e impuesto complementario'],

            // Para propietarios/arrendadores (personas jurídicas pequeñas)
            ['category' => 'tax_responsibility', 'name' => 'Régimen Simple', 'alias' => 'O-49', 'description' => 'Acogido al Régimen Simple de Tributación - SIMPLE'],

            // Opcional para grandes inmobiliarias
            ['category' => 'tax_responsibility', 'name' => 'Gran Contribuyente', 'alias' => 'O-13', 'description' => 'Contribuyentes clasificados como grandes por la DIAN'],
            ['category' => 'tax_responsibility', 'name' => 'Autorretenedor', 'alias' => 'O-47', 'description' => 'Autorretenedor en renta y complementarios'],
        ];

        foreach ($lookups as $lookup) {
            DB::table('lookups')->insert([
                'id' => Str::uuid(),
                'category' => $lookup['category'],
                'name' => $lookup['name'],
                'alias' => $lookup['alias'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
