<div class="space-y-4">
    <div class="grid grid-cols-3 items-center">
        <div class="flex gap-2 justify-self-start">
            <flux:link href="{{url()->previous('/')}}" variant="subtle" class="!flex gap-2">
                <flux:icon.arrow-uturn-left/>
                Go Back
            </flux:link>
        </div>
        <flux:heading size="xl" class="justify-self-center text-center">{{$title}}</flux:heading>
        <div class="justify-self-end text-right">
            {{$slot}}
        </div>
    </div>
    <flux:separator />
</div>
