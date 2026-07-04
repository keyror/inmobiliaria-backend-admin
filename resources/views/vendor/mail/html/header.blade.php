@props(['url'])
@php
    $logoPath = public_path('logo.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logoSrc)
<img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" style="max-height: 60px; max-width: 200px; width: auto; height: auto;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
