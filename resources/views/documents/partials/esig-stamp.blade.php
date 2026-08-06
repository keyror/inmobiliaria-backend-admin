{{--
  Sello de firma electrónica — se incluye debajo de cada imagen de firma.
  Usa margin-top negativo para solaparse con la parte inferior de la imagen (efecto marca de agua).
  Variables requeridas:
    $signatory   — objeto DocumentSignatory (status='signed')
    $saasLogoUri — data URI del logo del SaaS (puede ser null)
--}}
<table style="width:100%;border-collapse:collapse;margin-top:-20px;border:1px solid #b8c7e0;background:#eef2f8;">
  <tr>
    @if(!empty($saasLogoUri))
    <td style="width:26px;padding:2px 3px;vertical-align:middle;border-right:1px solid #c7d3e8;">
      <img src="{{ $saasLogoUri }}" style="display:block;max-height:16px;max-width:22px;">
    </td>
    @endif
    <td style="padding:2px 5px;vertical-align:middle;font-size:5.5pt;color:#1a3a5c;line-height:1.3;">
      <strong>Firma Electrónica</strong> &middot; {{ $signatory->signed_at->format('Y-m-d H:i:s') }} -05:00 &middot; Ley 527 de 1999
    </td>
  </tr>
</table>
