{{--
  Bloque de firma electrónica con logo como marca de agua.
  Variables:
    $imageUri    — data URI de la imagen de firma (null = no se renderiza nada)
    $signatory   — objeto DocumentSignatory con signed_at (null permitido)
    $saasLogoUri — data URI del logo del SaaS (null permitido)
--}}
@if(!empty($imageUri))
<div style="position:relative; width:100%; height:76px; overflow:hidden;">
  @if(!empty($saasLogoUri))
  <img src="{{ $saasLogoUri }}"
       style="position:absolute; top:8px; left:50%; width:110px; height:auto; margin-left:-55px; opacity:0.12; display:block;">
  @endif
  <div style="position:absolute; top:2px; left:0; right:0; text-align:center;">
    <img src="{{ $imageUri }}" style="display:inline-block; max-height:55px; max-width:180px;">
  </div>
  @if(isset($signatory) && $signatory?->signed_at)
  <div style="position:absolute; bottom:2px; left:0; right:0; text-align:center; font-size:5pt; color:#1a3a5c; font-weight:bold; opacity:0.75;">
    <strong>Firma Electrónica</strong> &middot; {{ $signatory->signed_at->format('Y-m-d H:i:s') }} -05:00 &middot; Ley 527 de 1999
  </div>
  @endif
</div>
@endif
