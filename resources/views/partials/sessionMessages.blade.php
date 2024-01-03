@if(session('success'))
    <div class="alert success">
        <a class="close" href=""></a>
        <dl>
            <dt>{{ session('success') }}</dt>
        </dl>
    </div>
@endif

@if(session('errors'))
    <div class="alert alert-danger my-2">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
