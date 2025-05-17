@if($errors->any())
    <div class="response-holder">
        @foreach($errors->all() as $error)
            <p class="response-text error">{{$error}}</p>
        @endforeach
    </div>
@endif
