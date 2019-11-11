@extends('layouts.users')

@section('content')
    <div class="container">
        {!! $html->table() !!}
    </div>
@endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
@endsection
@section('scripts')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    {!! $html->scripts() !!}
@endsection