{{-- A pdf.invoice that always blows up, used to test what happens when the template fails --}}
@php
    throw new \RuntimeException('template exploded');
@endphp
