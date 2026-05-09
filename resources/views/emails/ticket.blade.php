@component('mail::message')

# 🎵 Tu compra en Pulse

Hola, **{{ auth()->user()->name ?? 'músico' }}**. Tu pago ha sido procesado correctamente. ¡Gracias por apoyar la música independiente!

---

@component('mail::table')
| Artículo | Ud. | Total |
|:---------|:---:|------:|
@foreach($orders as $o)
| {{ $o->item_name }} | × {{ $o->quantity ?? 1 }} | {{ number_format($o->amount, 2, ',', '.') }} € |
@endforeach
@endcomponent

**Total pagado: {{ number_format($grandTotal, 2, ',', '.') }} €**

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Ir a Pulse
@endcomponent

---

@if($orders->first()->stripe_session_id)
*Referencia de pago: {{ $orders->first()->stripe_session_id }}*
@endif

Guarda este correo como justificante de tu compra.

Saludos,  
**El equipo de Pulse**

@endcomponent
