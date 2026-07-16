{{--
  Partial: dynamic-section.blade.php
  Variables: $clause (ContractClause), $rent (Rent), $company (Company)
  Renders one section block based on $clause->section_type.
--}}
@php
    $type   = $clause->section_type ?? 'clause';
    $config = is_array($clause->section_config) ? $clause->section_config : [];
    $heading = $clause->heading ?? '';

    $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
        ?? $rent->property->addresses->first();
    $tenantPairs = $rent->rentTenantCodebtors;
    $mainTenant  = $tenantPairs->first()?->tenant;
@endphp

{{-- ── separator ── --}}
@if($type === 'separator')
    @if(($config['style'] ?? 'line') === 'page_break')
        <div style="page-break-before: always;"></div>
    @else
        <hr class="divider-thin" style="margin: 10px 0;">
    @endif

{{-- ── header ── --}}
@elseif($type === 'header')
    <div class="section" style="text-align:center; margin-bottom:10px;">
        @if($heading)
            <div style="font-size:12pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">
                {{ $heading }}
            </div>
        @endif
        @if($clause->rendered_body)
            <div style="font-size:9pt; margin-top:4px;">{!! $clause->rendered_body !!}</div>
        @endif
    </div>

{{-- ── party_info ── --}}
@elseif($type === 'party_info')
    @php
        $role   = $config['role'] ?? 'arrendatario';
        $fields = $config['fields'] ?? ['name', 'document'];

        // Resolve person(s) by role
        $persons = collect();
        if ($role === 'arrendador') {
            // Company as landlord
            $persons = collect([['label' => 'ARRENDADOR', 'person' => null, 'isCompany' => true]]);
        } elseif ($role === 'propietario') {
            foreach ($rent->property->owners as $owner) {
                $persons->push(['label' => 'PROPIETARIO', 'person' => $owner, 'isCompany' => false]);
            }
        } elseif ($role === 'codeudor') {
            foreach ($tenantPairs as $pair) {
                if ($pair->codebtor) {
                    $persons->push(['label' => 'CODEUDOR', 'person' => $pair->codebtor, 'isCompany' => false]);
                }
            }
        } else {
            // arrendatario (default)
            foreach ($tenantPairs as $i => $pair) {
                if ($pair->tenant) {
                    $label = $tenantPairs->count() > 1 ? 'ARRENDATARIO ' . ($i + 1) : 'ARRENDATARIO';
                    $persons->push(['label' => $label, 'person' => $pair->tenant, 'isCompany' => false]);
                }
            }
        }
    @endphp

    <div class="section">
        @if($heading)
            <div class="section-title">{{ $heading }}</div>
        @endif

        @foreach($persons as $entry)
            @php $p = $entry['person']; $isCompany = $entry['isCompany']; @endphp
            <div class="section-subtitle mt-4">{{ $entry['label'] }}</div>
            <table class="data-table">
                @if($isCompany)
                    @if(in_array('name', $fields))
                    <tr>
                        <td class="label">Nombre / Razón Social:</td>
                        <td class="value">{{ $company->company_name }}</td>
                    </tr>
                    @endif
                    @if(in_array('document', $fields))
                    <tr>
                        <td class="label">NIT:</td>
                        <td class="value">{{ $company->nit }}</td>
                    </tr>
                    @endif
                    @if(in_array('representative', $fields) && $company->legalRepresentative)
                    <tr>
                        <td class="label">Representante Legal:</td>
                        <td class="value">{{ $company->legalRepresentative->full_name }}
                            — {{ $company->legalRepresentative->document_number }}</td>
                    </tr>
                    @endif
                @else
                    @if($p)
                        @if(in_array('name', $fields))
                        <tr>
                            <td class="label">Nombre / Razón Social:</td>
                            <td class="value">{{ $p->full_name ?? $p->company_name }}</td>
                        </tr>
                        @endif
                        @if(in_array('document', $fields))
                        <tr>
                            <td class="label">Documento:</td>
                            <td class="value">{{ $p->documentType?->alias ?? 'C.C.' }} {{ $p->document_number }}</td>
                        </tr>
                        @endif
                        @if(in_array('phone', $fields) && ($p->phone ?? null))
                        <tr>
                            <td class="label">Teléfono:</td>
                            <td class="value">{{ $p->phone }}</td>
                        </tr>
                        @endif
                        @if(in_array('email', $fields) && ($p->email ?? null))
                        <tr>
                            <td class="label">Correo electrónico:</td>
                            <td class="value">{{ $p->email }}</td>
                        </tr>
                        @endif
                        @if(in_array('address', $fields) && ($p->address ?? null))
                        <tr>
                            <td class="label">Dirección:</td>
                            <td class="value">{{ $p->address }}</td>
                        </tr>
                        @endif
                    @endif
                @endif
            </table>
        @endforeach
    </div>

{{-- ── property_info ── --}}
@elseif($type === 'property_info')
    @php
        $fields = $config['fields'] ?? ['address', 'city'];
        $prop   = $rent->property;
    @endphp

    <div class="section">
        @if($heading)
            <div class="section-title">{{ $heading }}</div>
        @endif
        <table class="data-table">
            @if(in_array('code', $fields))
            <tr>
                <td class="label">Código:</td>
                <td class="value">{{ $prop->code }}</td>
                @if(in_array('type', $fields))
                <td class="label">Tipo:</td>
                <td class="value">{{ $prop->propertyType?->name ?? '---' }}</td>
                @endif
            </tr>
            @endif
            @if(in_array('address', $fields))
            <tr>
                <td class="label">Dirección:</td>
                <td class="value" colspan="3">{{ $principalAddress?->address ?? '---' }}</td>
            </tr>
            @endif
            @if(in_array('city', $fields) || in_array('neighborhood', $fields))
            <tr>
                @if(in_array('city', $fields))
                <td class="label">Ciudad:</td>
                <td class="value">{{ $principalAddress?->city?->name ?? $prop->city?->name ?? '---' }}</td>
                @endif
                @if(in_array('neighborhood', $fields))
                <td class="label">Barrio:</td>
                <td class="value">{{ $principalAddress?->neighborhood ?? '---' }}</td>
                @endif
            </tr>
            @endif
            @if(in_array('registration', $fields))
            <tr>
                <td class="label">Matrícula inmobiliaria:</td>
                <td class="value">{{ $prop->registration_number ?? '---' }}</td>
                @if(in_array('stratum', $fields))
                <td class="label">Estrato:</td>
                <td class="value">{{ $prop->stratum?->name ?? '---' }}</td>
                @endif
            </tr>
            @endif
            @if(in_array('area', $fields) && ($prop->area ?? null))
            <tr>
                <td class="label">Área:</td>
                <td class="value">{{ $prop->area }} m²</td>
                <td class="label">Sometido a P.H.:</td>
                <td class="value">{{ $rent->is_ph ? 'Sí' : 'No' }}</td>
            </tr>
            @endif
            @if(in_array('destination', $fields))
            <tr>
                <td class="label">Destinación:</td>
                <td class="value">{{ ucfirst($rent->destination ?? '---') }}</td>
                @if($rent->activity && in_array('activity', $fields))
                <td class="label">Actividad:</td>
                <td class="value">{{ $rent->activity }}</td>
                @endif
            </tr>
            @endif
        </table>
    </div>

{{-- ── contract_info ── --}}
@elseif($type === 'contract_info')
    @php
        $fields        = $config['fields'] ?? ['canon', 'start_date', 'end_date', 'duration_months'];
        $canonFmt      = '$' . number_format($rent->canon ?? 0, 0, ',', '.');
        $ivaAmount     = $rent->iva ? $rent->canon * $rent->iva / 100 : 0;
        $totalFmt      = '$' . number_format(($rent->canon ?? 0) + $ivaAmount, 0, ',', '.');
    @endphp

    <div class="section">
        @if($heading)
            <div class="section-title">{{ $heading }}</div>
        @endif
        <table class="conditions-table">
            <tr>
                <th>Concepto</th>
                <th>Valor</th>
                <th>Concepto</th>
                <th>Valor</th>
            </tr>
            @if(in_array('canon', $fields))
            <tr>
                <td><strong>Canon mensual</strong></td>
                <td>{{ $canonFmt }}</td>
                @if($rent->iva && in_array('iva', $fields))
                <td>IVA ({{ $rent->iva }}%)</td>
                <td>${{ number_format($ivaAmount, 0, ',', '.') }}</td>
                @else
                <td>Total mensual</td>
                <td><strong>{{ $totalFmt }}</strong></td>
                @endif
            </tr>
            @endif
            @if(in_array('start_date', $fields) || in_array('end_date', $fields))
            <tr>
                @if(in_array('start_date', $fields))
                <td>Fecha de inicio</td>
                <td>{{ $rent->start_date?->format('d/m/Y') ?? '---' }}</td>
                @endif
                @if(in_array('end_date', $fields))
                <td>Fecha de terminación</td>
                <td>{{ $rent->end_date?->format('d/m/Y') ?? 'Término indefinido' }}</td>
                @endif
            </tr>
            @endif
            @if(in_array('duration_months', $fields))
            <tr>
                <td>Duración</td>
                <td>{{ $rent->duration ? $rent->duration . ' meses' : '---' }}</td>
                @if(in_array('increment_type', $fields))
                <td>Tipo de incremento</td>
                <td>{{ $rent->incrementType?->name ?? ($rent->interest_rate ?? 'IPC') }}</td>
                @endif
            </tr>
            @endif
            @if(in_array('admin_included', $fields))
            <tr>
                <td>Administración incluida</td>
                <td>{{ $rent->administration_included ? 'Sí' : 'No' }}</td>
                @if(in_array('payment_bank', $fields))
                <td>Banco de consignación</td>
                <td>{{ $rent->paymentBank?->name ?? '---' }}</td>
                @endif
            </tr>
            @endif
        </table>
    </div>

{{-- ── signature ── --}}
@elseif($type === 'signature')
    @php
        $signatories = $config['signatories'] ?? [
            ['role' => 'arrendador',   'label' => 'EL ARRENDADOR',   'side' => 'left'],
            ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right'],
        ];

        // Expand multi-person roles into one entry per person
        $allTenants   = $tenantPairs->filter(fn($p) => $p->tenant)->map(fn($p) => $p->tenant)->values();
        $allCodebtors = $tenantPairs->filter(fn($p) => $p->codebtor)->map(fn($p) => $p->codebtor)->values();
        $expanded = [];
        foreach ($signatories as $sig) {
            $role = $sig['role'] ?? 'arrendatario';
            if ($role === 'arrendatario' && $allTenants->isNotEmpty()) {
                foreach ($allTenants as $idx => $person) {
                    $lbl = $allTenants->count() > 1
                        ? ($sig['label'] ?? 'EL ARRENDATARIO') . ' ' . ($idx + 1)
                        : ($sig['label'] ?? 'EL ARRENDATARIO');
                    $expanded[] = array_merge($sig, ['label' => $lbl, '_person' => $person]);
                }
            } elseif ($role === 'codeudor' && $allCodebtors->isNotEmpty()) {
                foreach ($allCodebtors as $idx => $person) {
                    $lbl = $allCodebtors->count() > 1
                        ? ($sig['label'] ?? 'CODEUDOR') . ' ' . ($idx + 1)
                        : ($sig['label'] ?? 'CODEUDOR');
                    $expanded[] = array_merge($sig, ['label' => $lbl, '_person' => $person]);
                }
            } else {
                $expanded[] = $sig;
            }
        }
        $signatories = $expanded;

        // Resolve actual person data per signatory role
        $resolveSignatory = function (array $sig) use ($rent, $company, $tenantPairs): array {
            $role = $sig['role'] ?? 'arrendatario';
            $name = '___________________________';
            $doc  = '';
            $extra = '';

            if (isset($sig['_person'])) {
                $p    = $sig['_person'];
                $name = $p->full_name ?? $p->company_name ?? $name;
                $doc  = ($p->documentType?->alias ?? 'C.C.') . ' ' . ($p->document_number ?? '');
            } elseif ($role === 'arrendador' || $role === 'inmobiliaria') {
                $name  = $company->company_name ?? $name;
                $extra = $company->legalRepresentative
                    ? 'Rep. Legal: ' . $company->legalRepresentative->full_name
                    : '';
            } elseif ($role === 'arrendatario') {
                $t    = $tenantPairs->first()?->tenant;
                $name = $t ? ($t->full_name ?? $t->company_name ?? $name) : $name;
                $doc  = $t ? (($t->documentType?->alias ?? 'C.C.') . ' ' . $t->document_number) : '';
            } elseif ($role === 'codeudor') {
                $c    = $tenantPairs->first()?->codebtor;
                $name = $c ? ($c->full_name ?? $c->company_name ?? $name) : $name;
                $doc  = $c ? (($c->documentType?->alias ?? 'C.C.') . ' ' . $c->document_number) : '';
            } elseif ($role === 'propietario') {
                $o    = $rent->property->owners->first();
                $name = $o ? ($o->full_name ?? $o->company_name ?? $name) : $name;
                $doc  = $o ? (($o->documentType?->alias ?? 'C.C.') . ' ' . $o->document_number) : '';
            }

            $label = str_replace('{{NOMBRE_EMPRESA}}', $company->company_name ?? '', $sig['label'] ?? strtoupper($role));
            return ['name' => $name, 'doc' => $doc, 'extra' => $extra, 'label' => $label];
        };

        $leftSigs  = array_values(array_filter($signatories, fn($s) => ($s['side'] ?? 'left') === 'left'));
        $rightSigs = array_values(array_filter($signatories, fn($s) => ($s['side'] ?? 'left') === 'right'));
        $maxRows   = max(count($leftSigs), count($rightSigs), 1);
    @endphp

    <div class="section mt-8">
        @if($heading)
            <div class="section-title">{{ $heading }}</div>
        @endif
        @if($rent->signed_city || $rent->signed_at)
        <div class="text-small text-center" style="margin-bottom:6px;">
            El presente contrato se firma en la ciudad de
            <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
            el {{ $rent->signed_at?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
        </div>
        @endif
        @for($row = 0; $row < $maxRows; $row++)
        @php
            $leftData  = isset($leftSigs[$row])  ? $resolveSignatory($leftSigs[$row])  : null;
            $rightData = isset($rightSigs[$row]) ? $resolveSignatory($rightSigs[$row]) : null;
        @endphp
        <table class="sig-table" style="{{ $row > 0 ? 'margin-top:24px;' : '' }}">
            <tr>
                <td>
                    @if($leftData)
                    <div class="sig-line">
                        <div class="sig-name">{{ $leftData['name'] }}</div>
                        <div class="sig-role">{{ $leftData['label'] }}</div>
                        @if($leftData['doc'])
                        <div class="sig-doc">{{ $leftData['doc'] }}</div>
                        @endif
                        @if($leftData['extra'])
                        <div class="sig-doc">{{ $leftData['extra'] }}</div>
                        @endif
                    </div>
                    @endif
                </td>
                <td>
                    @if($rightData)
                    <div class="sig-line">
                        <div class="sig-name">{{ $rightData['name'] }}</div>
                        <div class="sig-role">{{ $rightData['label'] }}</div>
                        @if($rightData['doc'])
                        <div class="sig-doc">{{ $rightData['doc'] }}</div>
                        @endif
                        @if($rightData['extra'])
                        <div class="sig-doc">{{ $rightData['extra'] }}</div>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
        </table>
        @endfor
    </div>

{{-- ── table ── --}}
@elseif($type === 'table')
    <div class="section">
        @if($heading)
            <div class="section-title">{{ $heading }}</div>
        @endif
        @if($clause->rendered_body)
            <div style="font-size:9pt;">{!! $clause->rendered_body !!}</div>
        @endif
    </div>

{{-- ── clause / observation (default) ── --}}
@else
    <div class="clause">
        @if($heading)
            <span class="clause-num">{{ $heading }}</span>
        @endif
        {!! $clause->rendered_body !!}
    </div>
@endif
