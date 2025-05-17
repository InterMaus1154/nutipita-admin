@if($errors->any())
    @foreach($errors->all() as $error)
        <p class="response-text error">{{$error}}</p>
    @endforeach
@endif
