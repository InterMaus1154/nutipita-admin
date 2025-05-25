@if($errors->any())
    <div class="space-y-2">
        @foreach($errors->messages() as $key => $value)
            <flux:error name="{{$key}}">{{is_array($value) ? $value[0] : $value}}</flux:error>
        @endforeach
    </div>
@endif
