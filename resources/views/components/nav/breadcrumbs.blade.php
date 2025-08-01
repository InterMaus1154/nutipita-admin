<div class="flex flex-col gap-4">
    <div>
        @foreach($segments as $segment)
            @if($loop->first)
                <flux:link href="{{route($sectionIdentifier.'.index')}}">{{ucfirst($segment)}}</flux:link>
            @elseif($loop->last)
                > {{\Illuminate\Support\Str::headline(ucfirst($segment))}}
            @else
                > {{\Illuminate\Support\Str::headline(ucfirst($segment))}}
            @endif
        @endforeach
    </div>
    <div class="flex gap-2">
        <flux:icon.arrow-uturn-left/>
        <flux:link href="{{url()->previous('/')}}">Go Back</flux:link>
    </div>
</div>
