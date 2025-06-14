@if(session()->has('success'))
    <div class="flex gap-1 items-center">
        <flux:icon.check class="text-green-500 text-lg"/>
        <p class="text-lg font-bold text-green-500">{{session()->get('success')}}</p>
    </div>
@endif
