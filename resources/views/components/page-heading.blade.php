<div class="space-y-4">
    <div class="flex flex-wrap gap-4 sm:grid grid-cols-3 items-center">
        <div class="flex gap-2 justify-self-start">
            <flux:link href="{{url()->previous('/')}}" variant="subtle" class="!flex gap-2">
                <flux:icon.arrow-uturn-left/>
                Go Back
            </flux:link>
        </div>
        <h1 class="justify-self-center text-center text-lg sm:text-4xl font-bold">{{$title}}</h1>
        <div class="justify-self-end text-right">
            {{$slot}}
        </div>
    </div>
{{--    <flux:separator variant="subtle"/>--}}
</div>
