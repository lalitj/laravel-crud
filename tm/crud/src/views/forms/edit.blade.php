@extends ('layouts.app')

@section('content')






    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px;
            text-align: left;
        }
    </style>
    <div class="col-sm-8 blog-main">
        <h1> Add :<strong> {{--{{$project->name}}--}} </strong></h1>


        <div class="panel panel-default">
            <div class="panel-heading">
                <form method="POST" action="/{{$action}}">
                    {{csrf_field()}}


                @foreach($form as $name => $field)
                        <div class="form-group">
                            <label for="exampleInputEmail1">{{$field['label']}}</label>

                            @if($field['type'] == "select")

                                <select name="{{$name}}" class="form-control" id="">
                                    @foreach($field['options'] as $value => $label)
                                        <option @if($field['value'] == $value) selected @endif value="{{$value}}">{{$label}}</option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] == "multi")
                                <input type="hidden" name="{{$name}}" />
                                <select multiple class="form-control" id="" name="{{$name}}[]">
                                    @foreach($field['options'] as $value => $label)
                                        <option @if(in_array($value,$field['value'] ) )selected @endif value="{{$value}}">{{$label}}</option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] == "boolean")

                                <div class="form-check">
                                    <input type="checkbox" name="{{$name}}" class="form-check-input" id="" value="1" @if($field['value'] == 1) checked @endif>
                                    {{--&nbsp;<label class="form-check-label" for="exampleCheck1">{{$field['label']}}</label>--}}
                                </div>

                            @else

                                <input type="{{$field['type']}}" name="{{$name}}" value="{{$field['value']}}" class="form-control" id="" aria-describedby="" placeholder="{{$field['placeholder']}}">

                            @endif

                            @if(isset($field['text']))
                            <small id="emailHelp" class="form-text text-muted">{{$field['text']}}</small>
                            @endif



                        </div>



                    @endforeach


                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
                {{--<form method="POST" action="">
                    {{csrf_field()}}
                    <table class="table table-bordered">

                        --}}{{--@foreach($projects as $key => $project)--}}{{--
                    <div class="col-md-6">
                        --}}{{--<select name="projects" class="form-control">
                            @foreach($projects as $project)
                                <option  value="{{$project->id}}">{{$project->name}}</option>
                            @endforeach--}}{{--
                        --}}{{--<label>projects</label>
                        <input type="text" name="" value=""><br>
                        <label>clients</label>
                        @foreach($clients as $client)
                        <input type="text" name="" value="{{$client->name}}"><br>

                        @endforeach--}}{{--

                        --}}{{--<select>
                        <label>priority</label>
                        <input type="text" name="" value=""><br>
                        <label>delivery date</label>
                        <input type="time" name="delivery_date" value=""><br>

                           <label> status</label>
                        <input type="text" name="" value="">

                        </select>--}}{{--
                        <div class="form-group">
                            <label for="project"></label>

                            <select class="form-control" name="project" id="">
                                <label>client</label>
                                @foreach($clients as $client)
                                    <option  value="{{$client->id}}">{{$client->name}}</option><
                                @endforeach
                            </select>
                            <div class="form-group">
                                <label for="priority"></label>
                                <textarea class="form-control" name="priority" id="" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="delivery date"></label>
                                <input type="date" name="" id="" class="form-control" placeholder=""
                                       aria-describedby="helpId">
                        <label>status</label>
                                <input type="status" name="" id="" class="form-control" placeholder="">

                            </div>
                        </div>
                    </div>

              --}}{{--@endforeach--}}{{--
            </table>
                    <button type="submit">Update</button>
                </form>--}}
            </div>
        </div>
    </div>
@endsection()