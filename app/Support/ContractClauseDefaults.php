<?php

namespace App\Support;

class ContractClauseDefaults
{
    private array $defaults = [

        /* ------------------------------------------------------------------ */
        /* CONTRATOS */
        /* ------------------------------------------------------------------ */

        'arrendamiento_vivienda' => [
            // — Secciones estructurales (orden lógico del documento) —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario',   'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_codeudor',     'section_type' => 'party_info',    'heading' => 'Datos del Codeudor',       'body' => null, 'section_config' => ['role' => 'codeudor',     'fields' => ['name', 'document', 'phone', 'email']], 'sort_order' => 2],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',       'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 3],
            ['section_key' => 'struct_contract',     'section_type' => 'contract_info', 'heading' => 'Términos del Contrato',    'body' => null, 'section_config' => ['fields' => ['canon', 'start_date', 'end_date', 'duration_months', 'increment_type', 'admin_included']], 'sort_order' => 4],
            // — Cláusulas de texto —
            ['section_key' => 'primera',    'section_type' => 'clause', 'heading' => 'PRIMERA — Objeto.',                      'body' => 'El ARRENDADOR entrega al ARRENDATARIO el inmueble ubicado en la dirección señalada en la sección 2, para ser destinado exclusivamente a <strong>{{DESTINACION}}</strong>, conforme a lo dispuesto en la Ley 820 de 2003.',                                                                                                                                                                                                                                                                     'sort_order' => 5],
            ['section_key' => 'segunda',    'section_type' => 'clause', 'heading' => 'SEGUNDA — Canon y forma de pago.',        'body' => 'El ARRENDATARIO pagará mensualmente al ARRENDADOR la suma de <strong>{{CANON_MENSUAL}}</strong> {{ADMINISTRACION_INCLUIDA}}, mediante consignación a la cuenta indicada o al medio que señale el ARRENDADOR.',                                                                                                                                                                                                                                                                          'sort_order' => 6],
            ['section_key' => 'tercera',    'section_type' => 'clause', 'heading' => 'TERCERA — Incremento.',                   'body' => 'El valor del canon se incrementará anualmente conforme al {{TIPO_INCREMENTO}}, de acuerdo con lo establecido en el artículo 20 de la Ley 820 de 2003. La fecha de reajuste será el {{FECHA_REAJUSTE}}.',                                                                                                                                                                                                                                                                               'sort_order' => 7],
            ['section_key' => 'cuarta',     'section_type' => 'clause', 'heading' => 'CUARTA — Duración.',                      'body' => 'El término del presente contrato es de <strong>{{DURACION_MESES}}</strong>, contados a partir del <strong>{{FECHA_INICIO}}</strong> {{FECHA_FIN}}. Si ninguna de las partes da preaviso de no renovación con tres (3) meses de anticipación, el contrato se prorrogará automáticamente por períodos iguales.',                                                                                                                                                                          'sort_order' => 8],
            ['section_key' => 'quinta',     'section_type' => 'clause', 'heading' => 'QUINTA — Obligaciones del arrendatario.', 'body' => 'El ARRENDATARIO se obliga a: (a) pagar oportunamente el canon; (b) usar el inmueble conforme a la destinación pactada; (c) conservarlo en buen estado y responder por daños causados por su culpa o negligencia; (d) restituirlo al vencimiento en el mismo estado en que lo recibió salvo el deterioro natural; (e) no subarrendarlo sin autorización escrita del arrendador.',                                                                                                 'sort_order' => 9],
            ['section_key' => 'sexta',      'section_type' => 'clause', 'heading' => 'SEXTA — Obligaciones del arrendador.',    'body' => 'El ARRENDADOR se obliga a: (a) entregar el inmueble en buen estado; (b) mantenerlo en condiciones de servir al uso convenido; (c) garantizar el goce pacífico al arrendatario durante el contrato.',                                                                                                                                                                                                                                                                                  'sort_order' => 10],
            ['section_key' => 'septima',    'section_type' => 'clause', 'heading' => 'SÉPTIMA — Servicios públicos.',           'body' => 'Los servicios públicos domiciliarios (agua, luz, gas, teléfono) son de cargo del ARRENDATARIO, quien responderá por su pago oportuno y no podrá dejar deudas pendientes al momento de la restitución.',                                                                                                                                                                                                                                                                               'sort_order' => 11],
            ['section_key' => 'octava',     'section_type' => 'clause', 'heading' => 'OCTAVA — Codeudor.',                      'body' => 'El(los) CODEUDOR(ES) se constituyen en deudores solidarios de todas las obligaciones del ARRENDATARIO derivadas del presente contrato, incluyendo el pago del canon, daños, servicios y cualquier otra suma adeudada, sin necesidad de requerimiento previo.',                                                                                                                                                                                                                        'sort_order' => 12],
            ['section_key' => 'novena',     'section_type' => 'clause', 'heading' => 'NOVENA — Causales de terminación.',       'body' => 'El contrato podrá terminarse unilateralmente por las causales previstas en los artículos 22, 23 y 24 de la Ley 820 de 2003, incluyendo pero no limitado a: mora en el pago del canon, subarriendo sin autorización, destinación diferente a la pactada, o restitución voluntaria con preaviso.',                                                                                                                                                                                   'sort_order' => 13],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 14],
        ],

        'arrendamiento_comercial' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario',   'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_codeudor',     'section_type' => 'party_info',    'heading' => 'Datos del Codeudor',       'body' => null, 'section_config' => ['role' => 'codeudor',     'fields' => ['name', 'document', 'phone', 'email']], 'sort_order' => 2],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',       'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 3],
            ['section_key' => 'struct_contract',     'section_type' => 'contract_info', 'heading' => 'Términos del Contrato',    'body' => null, 'section_config' => ['fields' => ['canon', 'start_date', 'end_date', 'duration_months', 'increment_type', 'admin_included']], 'sort_order' => 4],
            // — Cláusulas —
            ['section_key' => 'primera',  'section_type' => 'clause', 'heading' => 'PRIMERA — Objeto.',                        'body' => 'El ARRENDADOR entrega al ARRENDATARIO el inmueble identificado en la sección 2, para que sea destinado exclusivamente a la actividad <strong>{{ACTIVIDAD_COMERCIAL}}</strong>, conforme al Código de Comercio, Decreto 410 de 1971.',                                                                                                                                                                                                                                               'sort_order' => 5],
            ['section_key' => 'segunda',  'section_type' => 'clause', 'heading' => 'SEGUNDA — Canon e IVA.',                   'body' => 'El ARRENDATARIO pagará mensualmente al ARRENDADOR la suma de <strong>{{CANON_MENSUAL}}</strong> como canon neto{{IVA_TEXTO}} {{ADMINISTRACION_INCLUIDA}}, mediante consignación a la cuenta indicada o al medio que señale el ARRENDADOR.',                                                                                                                                                                                                                                           'sort_order' => 6],
            ['section_key' => 'tercera',  'section_type' => 'clause', 'heading' => 'TERCERA — Incremento.',                    'body' => 'El valor del canon se incrementará anualmente conforme a lo pactado: <strong>{{TIPO_INCREMENTO}}</strong>, efectivo a partir del {{FECHA_REAJUSTE}}.',                                                                                                                                                                                                                                                                                                                               'sort_order' => 7],
            ['section_key' => 'cuarta',   'section_type' => 'clause', 'heading' => 'CUARTA — Duración.',                       'body' => 'El término del presente contrato es de <strong>{{DURACION_MESES}}</strong>, contados a partir del <strong>{{FECHA_INICIO}}</strong> {{FECHA_FIN}}. El contrato se prorrogará automáticamente por períodos iguales si ninguna de las partes da preaviso escrito de no renovación con la anticipación pactada o la establecida en la ley.',                                                                                                                                         'sort_order' => 8],
            ['section_key' => 'quinta',   'section_type' => 'clause', 'heading' => 'QUINTA — Obligaciones del arrendatario.',  'body' => 'El ARRENDATARIO se obliga a: (a) pagar el canon en la fecha acordada; (b) destinar el inmueble exclusivamente a la actividad comercial pactada; (c) conservarlo en buen estado; (d) no subarrendar sin autorización escrita; (e) pagar oportunamente los servicios públicos; (f) restituirlo al vencimiento en el mismo estado en que lo recibió salvo el deterioro natural.',         'sort_order' => 9],
            ['section_key' => 'sexta',    'section_type' => 'clause', 'heading' => 'SEXTA — Retención en la fuente e IVA.',    'body' => 'Las retenciones en la fuente que apliquen sobre el canon serán practicadas por quien actúe como agente retenedor, conforme a las disposiciones del Estatuto Tributario vigente. El IVA causado será declarado y pagado por el arrendador según su régimen tributario.',                                                                                                                                                                                                             'sort_order' => 10],
            ['section_key' => 'septima',  'section_type' => 'clause', 'heading' => 'SÉPTIMA — Codeudor.',                      'body' => 'El(los) CODEUDOR(ES) se constituyen en deudores solidarios de todas las obligaciones del ARRENDATARIO, respondiendo por el total de las mismas sin beneficio de excusión.',                                                                                                                                                                                                                                                                                                          'sort_order' => 11],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 12],
        ],

        'administracion_mandato' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_propietario', 'section_type' => 'party_info',    'heading' => 'Datos del Propietario (Mandante)', 'body' => null, 'section_config' => ['role' => 'propietario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',    'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',               'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',       'section_type' => 'clause', 'heading' => 'PRIMERA — Objeto del Mandato.',          'body' => 'El MANDANTE encarga a {{NOMBRE_EMPRESA}} la administración integral del inmueble identificado en la sección 2, incluyendo la búsqueda de arrendatarios, cobro de cánones, supervisión y demás gestiones necesarias para la correcta administración del bien.',                                                                                                                                                                                                                    'sort_order' => 3],
            ['section_key' => 'comision',     'section_type' => 'clause', 'heading' => 'SEGUNDA — Comisión de Administración.',   'body' => 'Como contraprestación por los servicios de administración, el MANDANTE reconocerá a {{NOMBRE_EMPRESA}} una comisión equivalente al {{COMISION_PORCENTAJE}}% del canon mensual efectivamente cobrado, la cual será descontada al momento de la liquidación mensual.',                                                                                                                                                                                                              'sort_order' => 4],
            ['section_key' => 'obligaciones', 'section_type' => 'clause', 'heading' => 'TERCERA — Obligaciones del Mandatario.', 'body' => 'El MANDATARIO se obliga a: (a) gestionar el arrendamiento del inmueble en condiciones favorables para el MANDANTE; (b) cobrar oportunamente los cánones; (c) liquidar mensualmente al MANDANTE el canon recibido menos la comisión pactada y las retenciones de ley; (d) mantener informado al MANDANTE sobre el estado del inmueble y del contrato.',                                                                                                                          'sort_order' => 5],
            ['section_key' => 'duracion',     'section_type' => 'clause', 'heading' => 'CUARTA — Vigencia.',                      'body' => 'El presente contrato tendrá una vigencia de {{DURACION_MESES}}, contados a partir del {{FECHA_INICIO}}, prorrogable automáticamente por períodos iguales salvo preaviso escrito de cualquiera de las partes con treinta (30) días de anticipación.',                                                                                                                                                                                                                              'sort_order' => 6],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'propietario', 'label' => 'EL MANDANTE (Propietario)', 'side' => 'left'], ['role' => 'arrendador', 'label' => 'EL MANDATARIO', 'side' => 'right']]], 'sort_order' => 7],
        ],

        'comodato' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Comodatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',   'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',       'section_type' => 'clause', 'heading' => 'PRIMERA — Objeto del Comodato.',          'body' => 'El COMODANTE entrega al COMODATARIO, a título de comodato o préstamo de uso gratuito, el inmueble identificado en la sección 2, para ser destinado exclusivamente a <strong>{{DESTINACION}}</strong>.', 'sort_order' => 3],
            ['section_key' => 'gratuidad',    'section_type' => 'clause', 'heading' => 'SEGUNDA — Gratuidad.',                    'body' => 'El presente comodato es esencialmente gratuito. El COMODATARIO no pagará ningún valor por el uso del inmueble. Los servicios públicos y demás gastos de funcionamiento correrán por cuenta del COMODATARIO.',    'sort_order' => 4],
            ['section_key' => 'duracion',     'section_type' => 'clause', 'heading' => 'TERCERA — Duración.',                     'body' => 'El presente contrato tendrá una vigencia de {{DURACION_MESES}}, contados a partir del {{FECHA_INICIO}} {{FECHA_FIN}}, al término de los cuales el COMODATARIO deberá restituir el inmueble en el mismo estado en que lo recibió.',                                                       'sort_order' => 5],
            ['section_key' => 'obligaciones', 'section_type' => 'clause', 'heading' => 'CUARTA — Obligaciones del Comodatario.', 'body' => 'El COMODATARIO se obliga a: (a) usar el inmueble únicamente para el fin pactado; (b) conservarlo con la diligencia de un buen padre de familia; (c) no subarrendarlo ni cederlo sin autorización escrita; (d) restituirlo al vencimiento en el mismo estado.',                               'sort_order' => 6],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL COMODANTE', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL COMODATARIO', 'side' => 'right']]], 'sort_order' => 7],
        ],

        'colocacion' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Propietario', 'body' => null, 'section_config' => ['role' => 'propietario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',   'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',     'section_type' => 'clause', 'heading' => 'PRIMERA — Objeto.',                  'body' => 'El presente contrato tiene por objeto la prestación del servicio de colocación de arrendatario por parte de {{NOMBRE_EMPRESA}} para el inmueble identificado en la sección 2, de propiedad del PROPIETARIO.', 'sort_order' => 3],
            ['section_key' => 'honorarios', 'section_type' => 'clause', 'heading' => 'SEGUNDA — Honorarios por Colocación.', 'body' => 'Como contraprestación por el servicio de colocación, el PROPIETARIO pagará a {{NOMBRE_EMPRESA}} la suma de <strong>{{HONORARIOS_COLOCACION}}</strong>, pagaderos una sola vez al momento de la firma del contrato de arrendamiento con el inquilino seleccionado.',  'sort_order' => 4],
            ['section_key' => 'alcance',    'section_type' => 'clause', 'heading' => 'TERCERA — Alcance del Servicio.',     'body' => 'El servicio de colocación comprende únicamente la búsqueda, selección y presentación del arrendatario al PROPIETARIO. La administración posterior del contrato de arrendamiento no está incluida en este servicio y deberá ser pactada por separado si así lo desean las partes.', 'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'propietario', 'label' => 'EL PROPIETARIO', 'side' => 'left'], ['role' => 'arrendador', 'label' => 'LA INMOBILIARIA', 'side' => 'right']]], 'sort_order' => 6],
        ],

        /* ------------------------------------------------------------------ */
        /* ACTAS */
        /* ------------------------------------------------------------------ */

        'entrega_inmueble' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',       'section_type' => 'clause', 'heading' => 'OBJETO.',                     'body' => 'En la ciudad de {{CIUDAD_FIRMA}}, siendo las partes conocedoras del contrato de arrendamiento N° {{NUMERO_CONTRATO}}, se procede a realizar la entrega formal del inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>, destinado a {{DESTINACION}}.', 'sort_order' => 3],
            ['section_key' => 'estado',       'section_type' => 'clause', 'heading' => 'ESTADO DEL INMUEBLE.',        'body' => 'El inmueble se entrega en las condiciones verificadas durante la diligencia de entrega. Cualquier observación sobre el estado inicial queda consignada en el acta y en el inventario adjunto. El ARRENDATARIO declara haber inspeccionado el inmueble y recibirlo a satisfacción.',                   'sort_order' => 4],
            ['section_key' => 'llaves',       'section_type' => 'clause', 'heading' => 'LLAVES Y ACCESOS.',           'body' => 'El ARRENDADOR hace entrega de las llaves y controles de acceso del inmueble. El ARRENDATARIO se obliga a entregar el mismo número de copias al momento de la devolución del inmueble.',                                                                                                                  'sort_order' => 5],
            ['section_key' => 'servicios',    'section_type' => 'clause', 'heading' => 'SERVICIOS PÚBLICOS.',         'body' => 'Se verifica el estado de los medidores y contadores de servicios públicos. A partir de la fecha de la presente acta, los servicios públicos son responsabilidad del ARRENDATARIO, quien se obliga a mantenerlos al día y a presentar los recibos de pago cuando el ARRENDADOR lo solicite.',              'sort_order' => 6],
            ['section_key' => 'obligaciones', 'section_type' => 'clause', 'heading' => 'OBLIGACIONES Y ACEPTACIÓN.', 'body' => 'El ARRENDATARIO acepta recibir el inmueble en las condiciones descritas y se compromete a devolverlo en iguales condiciones al término del contrato, salvo el deterioro natural por el uso. Las partes firman el presente documento en señal de conformidad.',                                             'sort_order' => 7],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 8],
        ],

        'devolucion_inmueble' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',   'section_type' => 'clause', 'heading' => 'OBJETO DE LA DEVOLUCIÓN.',              'body' => 'En la ciudad de {{CIUDAD_FIRMA}}, en la fecha señalada, el ARRENDATARIO {{NOMBRE_ARRENDATARIO}} procede a hacer entrega del inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>, dando por terminado el contrato de arrendamiento.', 'sort_order' => 3],
            ['section_key' => 'estado',   'section_type' => 'clause', 'heading' => 'ESTADO DEL INMUEBLE AL MOMENTO DE LA DEVOLUCIÓN.', 'body' => 'Las partes verifican el estado actual del inmueble comparándolo con el registrado en el acta de entrega. Los daños o deterioros que excedan el uso normal serán responsabilidad del ARRENDATARIO y deberán ser indemnizados.',                                 'sort_order' => 4],
            ['section_key' => 'deudas',   'section_type' => 'clause', 'heading' => 'VERIFICACIÓN DE DEUDAS Y SERVICIOS.',   'body' => 'El ARRENDATARIO declara que los servicios públicos se encuentran al día y se compromete a presentar los últimos recibos cancelados. Cualquier deuda pendiente de servicios o daños detectados en el inmueble será descontada del depósito o cobrada directamente al ARRENDATARIO.', 'sort_order' => 5],
            ['section_key' => 'finiquito', 'section_type' => 'clause', 'heading' => 'DECLARACIÓN DE PAZ Y SALVO.',           'body' => 'Una vez verificadas todas las obligaciones y canceladas las deudas pendientes, las partes declaran estar a paz y salvo respecto del contrato de arrendamiento, sin perjuicio de las acciones legales que pudieran surgir con posterioridad por hechos no detectados durante la diligencia.', 'sort_order' => 6],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 7],
        ],

        'inspeccion' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',        'section_type' => 'clause', 'heading' => 'OBJETO DE LA INSPECCIÓN.',            'body' => 'Se realiza inspección de rutina al inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>, correspondiente al contrato de arrendamiento con {{NOMBRE_ARRENDATARIO}}, con el propósito de verificar el estado de conservación y el cumplimiento de las obligaciones contractuales.', 'sort_order' => 3],
            ['section_key' => 'estado',        'section_type' => 'clause', 'heading' => 'ESTADO GENERAL DEL INMUEBLE.',        'body' => 'El inspector designado por {{NOMBRE_EMPRESA}} realizó una revisión detallada de las instalaciones, identificando el estado actual de cada componente. Las observaciones específicas y el estado de cada área se registran en el formulario adjunto de inspección.',                      'sort_order' => 4],
            ['section_key' => 'observaciones', 'section_type' => 'clause', 'heading' => 'OBSERVACIONES Y RECOMENDACIONES.',    'body' => 'Como resultado de la inspección, se emiten las siguientes recomendaciones al ARRENDATARIO para el adecuado mantenimiento del inmueble. El incumplimiento de estas recomendaciones podrá constituir causal de terminación del contrato o de cobro de daños al vencimiento.',              'sort_order' => 5],
            ['section_key' => 'compromisos',   'section_type' => 'clause', 'heading' => 'COMPROMISOS ADQUIRIDOS.',              'body' => 'Las partes acuerdan los compromisos derivados de la presente inspección. El ARRENDATARIO se obliga a ejecutar las correcciones solicitadas dentro del plazo acordado. {{NOMBRE_EMPRESA}} realizará una inspección de seguimiento para verificar el cumplimiento.',                     'sort_order' => 6],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 7],
        ],

        /* ------------------------------------------------------------------ */
        /* FACTURAS */
        /* ------------------------------------------------------------------ */

        'canon' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'concepto', 'section_type' => 'clause', 'heading' => 'CONCEPTO.',             'body' => 'Canon de arrendamiento correspondiente al período de facturación del contrato suscrito con {{NOMBRE_ARRENDATARIO}} para el inmueble ubicado en {{DIRECCION_INMUEBLE}}.', 'sort_order' => 3],
            ['section_key' => 'desglose', 'section_type' => 'clause', 'heading' => 'DESGLOSE DEL VALOR.',   'body' => 'Canon mensual: <strong>{{CANON_MENSUAL}}</strong>. {{IVA_TEXTO}} Total a pagar: <strong>{{TOTAL_MENSUAL}}</strong>.',                                                    'sort_order' => 4],
            ['section_key' => 'pago',     'section_type' => 'clause', 'heading' => 'INSTRUCCIONES DE PAGO.', 'body' => 'El pago deberá realizarse dentro del plazo establecido en el contrato de arrendamiento. Pasada la fecha límite se causarán intereses de mora a la tasa máxima permitida por la ley. Pagos a través de los medios indicados por {{NOMBRE_EMPRESA}}.', 'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'LA INMOBILIARIA', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'Recibido por', 'side' => 'right']]], 'sort_order' => 6],
        ],

        'liquidacion' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'intro',   'section_type' => 'clause', 'heading' => 'LIQUIDACIÓN FINAL DEL CONTRATO.', 'body' => 'La presente liquidación corresponde al cierre definitivo del contrato de arrendamiento con {{NOMBRE_ARRENDATARIO}} para el inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>. Se relacionan a continuación todos los cargos y abonos que determinan el saldo final a pagar o a devolver.', 'sort_order' => 3],
            ['section_key' => 'detalle', 'section_type' => 'clause', 'heading' => 'DETALLE DE CARGOS Y ABONOS.',      'body' => 'Los cargos y abonos relacionados han sido verificados contra los registros del contrato, los recibos de pago y las actas suscritas durante la vigencia del mismo. Cualquier discrepancia deberá ser comunicada a {{NOMBRE_EMPRESA}} dentro de los cinco (5) días hábiles siguientes a la recepción de este documento.',                                 'sort_order' => 4],
            ['section_key' => 'saldo',   'section_type' => 'clause', 'heading' => 'SALDO FINAL.',                     'body' => 'El saldo resultante deberá ser cancelado o devuelto, según sea el caso, dentro de los diez (10) días hábiles siguientes a la firma de la presente liquidación. Las partes declaran que esta liquidación constituye el cierre definitivo de las obligaciones económicas del contrato.',                                                                  'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'LA INMOBILIARIA', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 6],
        ],

        /* ------------------------------------------------------------------ */
        /* PÓLIZAS */
        /* ------------------------------------------------------------------ */

        'seguro_arrendamiento' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',    'section_type' => 'clause', 'heading' => 'OBJETO DE LA PÓLIZA.',    'body' => 'La presente póliza de seguro de arrendamiento ampara al ARRENDADOR y a {{NOMBRE_EMPRESA}} frente al riesgo de incumplimiento en el pago del canon mensual por parte del ARRENDATARIO {{NOMBRE_ARRENDATARIO}}, para el inmueble ubicado en {{DIRECCION_INMUEBLE}}.',                                                                             'sort_order' => 3],
            ['section_key' => 'cobertura', 'section_type' => 'clause', 'heading' => 'COBERTURAS.',             'body' => 'La póliza cubre: (a) incumplimiento en el pago del canon mensual; (b) daños al inmueble causados por el asegurado; (c) servicios públicos impagos al momento de la devolución. La suma asegurada y los deducibles se detallan en el certificado de la póliza adjunto.', 'sort_order' => 4],
            ['section_key' => 'vigencia',  'section_type' => 'clause', 'heading' => 'VIGENCIA Y CONDICIONES.', 'body' => 'La póliza tiene vigencia desde el {{FECHA_INICIO}} y cubre el período del contrato de arrendamiento. Para hacer efectiva la póliza, el ARRENDADOR deberá presentar la reclamación dentro de los plazos establecidos por la aseguradora, acompañada de la documentación requerida.',                                                         'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 6],
        ],

        /* ------------------------------------------------------------------ */
        /* GARANTÍAS */
        /* ------------------------------------------------------------------ */

        'codeudor' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_codeudor',     'section_type' => 'party_info',    'heading' => 'Datos del Codeudor',     'body' => null, 'section_config' => ['role' => 'codeudor',     'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 2],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 3],
            // — Cláusulas —
            ['section_key' => 'declaracion', 'section_type' => 'clause', 'heading' => 'DECLARACIÓN DE CODEUDORÍA.', 'body' => 'El(los) codeudor(es) {{NOMBRE_CODEUDOR}}, identificado(s) con {{DOCUMENTO_CODEUDOR}}, se constituye(n) en deudor(es) solidario(s) del ARRENDATARIO {{NOMBRE_ARRENDATARIO}} frente a {{NOMBRE_EMPRESA}}, respondiendo por el cumplimiento de todas las obligaciones derivadas del contrato de arrendamiento del inmueble ubicado en {{DIRECCION_INMUEBLE}}.', 'sort_order' => 4],
            ['section_key' => 'alcance',     'section_type' => 'clause', 'heading' => 'ALCANCE DE LA GARANTÍA.',      'body' => 'La codeudoría comprende todas las obligaciones del ARRENDATARIO, incluyendo pero no limitándose a: pago del canon, daños al inmueble, servicios públicos, y cualquier otra suma adeudada al vencimiento o durante la vigencia del contrato. El codeudor responde por el total de la obligación sin beneficio de excusión.', 'sort_order' => 5],
            ['section_key' => 'vigencia',    'section_type' => 'clause', 'heading' => 'VIGENCIA DE LA GARANTÍA.',     'body' => 'La presente garantía de codeudoría estará vigente durante todo el tiempo que dure el contrato de arrendamiento, incluyendo sus prórrogas, y hasta que se extingan completamente todas las obligaciones derivadas del mismo. No se requerirá notificación previa al codeudor para hacer exigible su responsabilidad.', 'sort_order' => 6],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'left'], ['role' => 'codeudor', 'label' => 'EL CODEUDOR', 'side' => 'right']]], 'sort_order' => 7],
        ],

        /* ------------------------------------------------------------------ */
        /* INVENTARIO */
        /* ------------------------------------------------------------------ */

        'inventario_inmueble' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'objeto',     'section_type' => 'clause', 'heading' => 'OBJETO DEL INVENTARIO.',                      'body' => 'El presente inventario registra el estado de los elementos, instalaciones y acabados del inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>, entregado en arrendamiento a {{NOMBRE_ARRENDATARIO}}. Sirve como referencia para determinar el estado inicial y final del inmueble durante el contrato.',                                                               'sort_order' => 3],
            ['section_key' => 'estado',     'section_type' => 'clause', 'heading' => 'ESTADO GENERAL AL MOMENTO DEL INVENTARIO.',   'body' => 'El inventario fue realizado mediante inspección física del inmueble en presencia de las partes. Los ítems se califican según su estado: Excelente (E), Bueno (B), Regular (R) o Malo (M). Cualquier observación específica se consigna en la columna de notas del inventario adjunto.', 'sort_order' => 4],
            ['section_key' => 'aceptacion', 'section_type' => 'clause', 'heading' => 'ACEPTACIÓN DEL INVENTARIO.',                  'body' => 'Las partes declaran haber revisado y aceptado el presente inventario. El ARRENDATARIO reconoce haber recibido el inmueble con los elementos descritos y en el estado consignado. Cualquier discrepancia deberá ser comunicada por escrito a {{NOMBRE_EMPRESA}} dentro de los tres (3) días hábiles siguientes a la recepción.', 'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right']]], 'sort_order' => 6],
        ],

        /* ------------------------------------------------------------------ */
        /* PREAVISO */
        /* ------------------------------------------------------------------ */

        'preaviso_terminacion' => [
            // — Secciones estructurales —
            ['section_key' => 'struct_arrendatario', 'section_type' => 'party_info',    'heading' => 'Datos del Arrendatario', 'body' => null, 'section_config' => ['role' => 'arrendatario', 'fields' => ['name', 'document', 'phone', 'email', 'address']], 'sort_order' => 1],
            ['section_key' => 'struct_property',     'section_type' => 'property_info', 'heading' => 'Datos del Inmueble',    'body' => null, 'section_config' => ['fields' => ['address', 'city', 'neighborhood', 'registration', 'type', 'area']], 'sort_order' => 2],
            // — Cláusulas —
            ['section_key' => 'notificacion', 'section_type' => 'clause', 'heading' => 'NOTIFICACIÓN DE TERMINACIÓN.', 'body' => 'Por medio de la presente comunicación, {{NOMBRE_EMPRESA}}, en representación del ARRENDADOR, notifica formalmente al ARRENDATARIO {{NOMBRE_ARRENDATARIO}} su decisión de no renovar el contrato de arrendamiento del inmueble ubicado en <strong>{{DIRECCION_INMUEBLE}}</strong>, el cual vence el {{FECHA_FIN}}.', 'sort_order' => 3],
            ['section_key' => 'fundamento',   'section_type' => 'clause', 'heading' => 'FUNDAMENTO LEGAL.',             'body' => 'La presente notificación se emite conforme a lo establecido en el artículo 22 de la Ley 820 de 2003 para contratos de vivienda urbana (o el Código de Comercio para contratos comerciales), que establece la obligación de dar preaviso escrito con la anticipación mínima requerida antes del vencimiento del contrato.',             'sort_order' => 4],
            ['section_key' => 'plazo',        'section_type' => 'clause', 'heading' => 'PLAZO Y OBLIGACIONES.',         'body' => 'El ARRENDATARIO deberá proceder a la desocupación y entrega del inmueble a más tardar en la fecha de terminación indicada, en las mismas condiciones en que lo recibió, al tenor del acta de entrega. Se le recuerda igualmente su obligación de dejar los servicios públicos al día y devolver la totalidad de las llaves del inmueble.',      'sort_order' => 5],
            // — Firma —
            ['section_key' => 'struct_signature', 'section_type' => 'signature', 'heading' => 'Firmas', 'body' => null, 'section_config' => ['signatories' => [['role' => 'arrendador', 'label' => 'LA INMOBILIARIA', 'side' => 'left'], ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO (Notificado)', 'side' => 'right']]], 'sort_order' => 6],
        ],
    ];

    public function getDefaults(string $templateKey): ?array
    {
        return $this->defaults[$templateKey] ?? null;
    }

    /** @return array<string, array{label: string, category: string}> */
    public function getAvailableTemplates(): array
    {
        return [
            'arrendamiento_vivienda' => ['label' => 'Arrendamiento Vivienda (Ley 820/2003)', 'category' => 'contrato'],
            'arrendamiento_comercial' => ['label' => 'Arrendamiento Comercial (Decreto 410/1971)', 'category' => 'contrato'],
            'administracion_mandato' => ['label' => 'Administración / Mandato', 'category' => 'contrato'],
            'comodato' => ['label' => 'Comodato', 'category' => 'contrato'],
            'colocacion' => ['label' => 'Colocación', 'category' => 'contrato'],
            'entrega_inmueble' => ['label' => 'Acta de Entrega', 'category' => 'acta'],
            'devolucion_inmueble' => ['label' => 'Acta de Devolución', 'category' => 'acta'],
            'inspeccion' => ['label' => 'Acta de Inspección', 'category' => 'acta'],
            'canon' => ['label' => 'Factura de Canon', 'category' => 'factura'],
            'liquidacion' => ['label' => 'Liquidación Final', 'category' => 'factura'],
            'seguro_arrendamiento' => ['label' => 'Póliza de Seguro', 'category' => 'poliza'],
            'codeudor' => ['label' => 'Garantía Codeudor', 'category' => 'garantia'],
            'inventario_inmueble' => ['label' => 'Inventario de Inmueble', 'category' => 'inventario'],
            'preaviso_terminacion' => ['label' => 'Preaviso de Terminación', 'category' => 'preaviso'],
        ];
    }

    /**
     * Variables grouped by category for the frontend variable picker.
     *
     * @return array<int, array{group: string, icon: string, variables: array<int, array{id: string, label: string}>}>
     */
    public function getVariableGroups(): array
    {
        return [
            [
                'group' => 'Arrendatario / Cliente',
                'icon' => 'user',
                'variables' => [
                    ['id' => 'tenant.name', 'label' => 'Nombre completo'],
                    ['id' => 'tenant.document', 'label' => 'Tipo y número de documento'],
                    ['id' => 'tenant.phone', 'label' => 'Teléfono'],
                    ['id' => 'tenant.email', 'label' => 'Correo electrónico'],
                ],
            ],
            [
                'group' => 'Codeudor',
                'icon' => 'user-shield',
                'variables' => [
                    ['id' => 'codebtor.name', 'label' => 'Nombre completo'],
                    ['id' => 'codebtor.document', 'label' => 'Tipo y número de documento'],
                ],
            ],
            [
                'group' => 'Propietario',
                'icon' => 'user-tie',
                'variables' => [
                    ['id' => 'owner.name', 'label' => 'Nombre completo'],
                    ['id' => 'owner.document', 'label' => 'Tipo y número de documento'],
                ],
            ],
            [
                'group' => 'Inmueble',
                'icon' => 'building',
                'variables' => [
                    ['id' => 'property.address', 'label' => 'Dirección'],
                    ['id' => 'property.city', 'label' => 'Municipio / Ciudad'],
                    ['id' => 'property.neighborhood', 'label' => 'Barrio'],
                    ['id' => 'property.registration', 'label' => 'Matrícula inmobiliaria'],
                ],
            ],
            [
                'group' => 'Contrato',
                'icon' => 'file-contract',
                'variables' => [
                    ['id' => 'contract.canon', 'label' => 'Canon mensual'],
                    ['id' => 'contract.total', 'label' => 'Total mensual (canon + IVA)'],
                    ['id' => 'contract.iva_pct', 'label' => 'Porcentaje de IVA'],
                    ['id' => 'contract.iva_text', 'label' => 'Texto del IVA'],
                    ['id' => 'contract.start_date', 'label' => 'Fecha de inicio'],
                    ['id' => 'contract.end_date', 'label' => 'Fecha de terminación'],
                    ['id' => 'contract.duration', 'label' => 'Duración en meses'],
                    ['id' => 'contract.increment_type', 'label' => 'Tipo de incremento'],
                    ['id' => 'contract.adjustment_date', 'label' => 'Fecha de reajuste'],
                    ['id' => 'contract.destination', 'label' => 'Destinación del inmueble'],
                    ['id' => 'contract.activity', 'label' => 'Actividad comercial'],
                    ['id' => 'contract.admin_included', 'label' => 'Administración incluida'],
                    ['id' => 'contract.commission_pct', 'label' => 'Comisión (%)'],
                    ['id' => 'contract.placement_fee', 'label' => 'Honorarios por colocación'],
                    ['id' => 'contract.signed_city', 'label' => 'Ciudad de firma'],
                    ['id' => 'contract.signed_date', 'label' => 'Fecha de firma'],
                ],
            ],
            [
                'group' => 'Empresa (Inmobiliaria)',
                'icon' => 'briefcase',
                'variables' => [
                    ['id' => 'company.name', 'label' => 'Nombre de la empresa'],
                    ['id' => 'company.nit', 'label' => 'NIT'],
                    ['id' => 'company.address', 'label' => 'Dirección'],
                    ['id' => 'company.phone', 'label' => 'Teléfono'],
                ],
            ],
        ];
    }

    /**
     * Maps dotted-key variable ids (used in Tiptap JSON) to their {{PLACEHOLDER}} equivalents.
     *
     * @return array<string, string>
     */
    public function getDottedToPlaceholderMap(): array
    {
        return [
            'tenant.name' => '{{NOMBRE_ARRENDATARIO}}',
            'tenant.document' => '{{DOCUMENTO_ARRENDATARIO}}',
            'tenant.phone' => '{{TELEFONO_ARRENDATARIO}}',
            'tenant.email' => '{{EMAIL_ARRENDATARIO}}',
            'codebtor.name' => '{{NOMBRE_CODEUDOR}}',
            'codebtor.document' => '{{DOCUMENTO_CODEUDOR}}',
            'owner.name' => '{{NOMBRE_PROPIETARIO}}',
            'owner.document' => '{{DOCUMENTO_PROPIETARIO}}',
            'property.address' => '{{DIRECCION_INMUEBLE}}',
            'property.city' => '{{MUNICIPIO_INMUEBLE}}',
            'property.neighborhood' => '{{BARRIO_INMUEBLE}}',
            'property.registration' => '{{MATRICULA_INMUEBLE}}',
            'contract.canon' => '{{CANON_MENSUAL}}',
            'contract.total' => '{{TOTAL_MENSUAL}}',
            'contract.iva_pct' => '{{IVA_PORCENTAJE}}',
            'contract.iva_text' => '{{IVA_TEXTO}}',
            'contract.start_date' => '{{FECHA_INICIO}}',
            'contract.end_date' => '{{FECHA_FIN}}',
            'contract.duration' => '{{DURACION_MESES}}',
            'contract.increment_type' => '{{TIPO_INCREMENTO}}',
            'contract.adjustment_date' => '{{FECHA_REAJUSTE}}',
            'contract.destination' => '{{DESTINACION}}',
            'contract.activity' => '{{ACTIVIDAD_COMERCIAL}}',
            'contract.admin_included' => '{{ADMINISTRACION_INCLUIDA}}',
            'contract.commission_pct' => '{{COMISION_PORCENTAJE}}',
            'contract.placement_fee' => '{{HONORARIOS_COLOCACION}}',
            'contract.signed_city' => '{{CIUDAD_FIRMA}}',
            'contract.signed_date' => '{{FECHA_FIRMA}}',
            'company.name' => '{{NOMBRE_EMPRESA}}',
            'company.nit' => '{{NIT_EMPRESA}}',
            'company.address' => '{{DIRECCION_EMPRESA}}',
            'company.phone' => '{{TELEFONO_EMPRESA}}',
        ];
    }

    /** @return array<string, string> — backward-compatible flat list */
    public function getVariableDescriptions(): array
    {
        return [
            '{{CANON_MENSUAL}}' => 'Canon mensual formateado (ej: $750.000)',
            '{{TOTAL_MENSUAL}}' => 'Total mensual (canon + IVA)',
            '{{IVA_PORCENTAJE}}' => 'Porcentaje de IVA',
            '{{IVA_TEXTO}}' => 'Texto del IVA (solo contratos comerciales con IVA)',
            '{{DESTINACION}}' => 'Destinación del inmueble (ej: vivienda urbana)',
            '{{ACTIVIDAD_COMERCIAL}}' => 'Actividad comercial del arrendatario',
            '{{ADMINISTRACION_INCLUIDA}}' => 'Texto indicando si la administración está incluida',
            '{{TIPO_INCREMENTO}}' => 'Tipo de incremento del canon (ej: IPC)',
            '{{FECHA_REAJUSTE}}' => 'Fecha de reajuste del canon',
            '{{DURACION_MESES}}' => 'Duración del contrato en meses',
            '{{FECHA_INICIO}}' => 'Fecha de inicio del contrato',
            '{{FECHA_FIN}}' => 'Fecha de terminación del contrato',
            '{{NOMBRE_ARRENDATARIO}}' => 'Nombre completo del arrendatario',
            '{{DOCUMENTO_ARRENDATARIO}}' => 'Tipo y número de documento del arrendatario',
            '{{TELEFONO_ARRENDATARIO}}' => 'Teléfono del arrendatario',
            '{{EMAIL_ARRENDATARIO}}' => 'Correo electrónico del arrendatario',
            '{{NOMBRE_CODEUDOR}}' => 'Nombre completo del codeudor',
            '{{DOCUMENTO_CODEUDOR}}' => 'Tipo y número de documento del codeudor',
            '{{NOMBRE_PROPIETARIO}}' => 'Nombre completo del propietario',
            '{{DOCUMENTO_PROPIETARIO}}' => 'Tipo y número de documento del propietario',
            '{{NOMBRE_EMPRESA}}' => 'Nombre de la inmobiliaria',
            '{{NIT_EMPRESA}}' => 'NIT de la inmobiliaria',
            '{{DIRECCION_EMPRESA}}' => 'Dirección de la inmobiliaria',
            '{{TELEFONO_EMPRESA}}' => 'Teléfono de la inmobiliaria',
            '{{DIRECCION_INMUEBLE}}' => 'Dirección del inmueble',
            '{{MUNICIPIO_INMUEBLE}}' => 'Municipio del inmueble',
            '{{BARRIO_INMUEBLE}}' => 'Barrio del inmueble',
            '{{MATRICULA_INMUEBLE}}' => 'Matrícula inmobiliaria',
            '{{CIUDAD_FIRMA}}' => 'Ciudad donde se firma el contrato',
            '{{FECHA_FIRMA}}' => 'Fecha de firma del contrato',
            '{{COMISION_PORCENTAJE}}' => 'Porcentaje de comisión de administración',
            '{{HONORARIOS_COLOCACION}}' => 'Valor de honorarios por colocación',
        ];
    }
}
