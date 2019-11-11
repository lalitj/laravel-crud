{{--extends ('layouts.app')

@section('content')

    <div class="container">
        {!! Table::generate($headers, $data, $attributes) !!}


    </div>

@endsection

@section('styles')
    <!-- Data Table CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            } );
        } );

    </script>
@endsection--}}
@isset($no_data)
    @push("content")
        No Data
    @endpush
@else

    @push("content")
        {{--<div class="container">--}}
        <div class="filters">
            <table class="table table-bordered table-sm">
                <thead>
                <tr>
                    @foreach($filters as $filter)
                        <th>
                            @if($filter['type']=="select")
                                <label for="filter-student-full_name">{{$filter['title']}}</label><br>
                                <select id="filter-{{$filter['filter']}}">
                                    @foreach($filter['options'] as $option_key => $option)
                                    <option @if(isset($filter['id']) && $filter['id'])value="{{$option_key}}" @endif>{{$option}}</option>
                                    @endforeach
                                </select>
                            @elseif($filter['type']=="date-between")
                                <label for="filter-{{$filter['filter']}}">{{$filter['title']}}</label><br>
                                <input type="date" id="filter-{{$filter['filter']}}-start" value="">
                                <input type="date" id="filter-{{$filter['filter']}}-end" value="">
                            @else
                                <label for="filter-{{$filter['filter']}}">{{$filter['title']}}</label><br>
                                <input type="{{$filter['type']}}" id="filter-{{$filter['filter']}}" value="">
                            @endif
                        </th>
                    @endforeach
                </tr>
                </thead>
            </table>
        </div>


        {!! $html->table() !!}
        {{--</div>--}}
    @endpush

    @push("styles")
        <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
    @endpush

    @push("scripts")
        <script type="text/javascript">
            var dataColumns = '{!! json_encode($headers) !!}';
        </script>
        <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js" type="text/javascript"></script>
        <script src="{{ asset("js/jquery.dataTables.columnFilter.js") }}" type="text/javascript"></script>
        <script src="//cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js" type="text/javascript"></script>
        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" type="text/javascript"></script>
        <script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js" type="text/javascript"></script>
        <script src="{{ asset("js/datatable-code.js") }}" type="text/javascript"></script>
    @endpush
@endif
