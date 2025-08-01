@use(Illuminate\Support\Str)
<div class="flex flex-col gap-4">
    <div>
        @foreach($segments as $segment)
            @if($loop->first)
                <flux:link href="{{route($sectionIdentifier.'.index')}}">{{ucfirst($segment)}}</flux:link>
            @else
                > {{Str::headline(ucfirst($segment))}}
            @endif
        @endforeach
    </div>
    <div class="flex gap-2">
        <flux:icon.arrow-uturn-left/>
        <flux:link href="{{url()->previous('/')}}" variant="subtle">
            Go Back
        </flux:link>
    </div>
</div>
