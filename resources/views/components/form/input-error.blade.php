@props([
    'message' => ''
])

<p {{$attributes->merge([
    'class' => 'text-red-500 font-bold text-md'
])}}>{{$message}}</p>
