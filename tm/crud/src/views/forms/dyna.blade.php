@extends ('layouts.users')

@section('content')


    <div class="row">
        <div class="col">
    {!! Table::generate($headers, $data, $attributes) !!}
</div>
    </div>

    @endsection