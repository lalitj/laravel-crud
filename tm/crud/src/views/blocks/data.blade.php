@if(!isset($layout) || !$layout)
    @php($layout = "layouts.users")
@endif
@extends($layout)

@include("tm::forms.datatable")
@include("tm::blocks.confirm")

@section("content")
    @stack("content")
@endsection

@section("styles")
    @stack("styles")
@endsection

@section("scripts")
    @stack("scripts")
@endsection