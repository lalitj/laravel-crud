
@php($name = $field['name'])

@php($id = "")
@if(isset($field["id"]))
    @php($id = $field['id'])
@endif

@if($id)
    @php($form_group_id = $id."-form-group")
@else
    @php($form_group_id = $name."-form-group")
@endif

@if($field['type'] != "select" && $field['type'] != "multi-select" && is_array($field['value']))
    Silly Mistake! Please use string instead of array for value parameter in field: <strong>{{$field['name']}}</strong>!
    {{pre($field)}}
    {{dd()}}
@endif

{{--Set Value--}}
@if(old($name))
    @php($value = old($name) )
@elseif(isset($field['value']))
    @php($value = $field['value'])
@else
    @php($value = "")
@endif

@php($field['value'] = $value)

@php($validation_class = $errors->has($name) ? ' is-invalid' : '')


<style>
    .form-group .invalid-feedback {
        display: block;
    }

    .form-group .is-invalid {
        border-color: #dc3545;
    }
</style>
@php($form_group_class = "")
@isset($field['form_group_class'])
    @php($form_group_class = $field['form_group_class'])
@endisset


<div class="col_full">
                    <div id="{{$form_group_id}}" class="col_full {{$form_group_class}}">
                        @if(isset($field['label']))<label for="exampleInputEmail1">{{$field['label']}}</label>@endif

                        @if($field['type'] == "select")
                            <select name="{{$name}}"
                                    class="form-control @if(isset($field['class'])){{$field['class']}}@endif{{$validation_class}}"
                                    id="@if(isset($field['id'])){{$field['id']}}@endif">
                                @foreach($field['options'] as $value => $label)
                                    <option @if($field['value'] == $value) selected
                                            @endif value="{{$value}}">{{$label}}</option>
                                @endforeach
                            </select>

                        @elseif($field['type'] == "multi")
                            <input type="hidden" name="{{$name}}"/>
                            <select multiple class="form-control" id="{{$id}}" name="{{$name}}[]">
                                @foreach($field['options'] as $value => $label)
                                    <option @if(in_array($value,$field['value'] ) )selected
                                            @endif value="{{$value}}">{{$label}}</option>
                                @endforeach
                            </select>

                        @elseif($field['type'] == "boolean" || $field['type'] == "checkbox")
                            <div class="form-check">
                                <input type="checkbox" name="{{$name}}" class="form-check-input"
                                       id="@if(isset($field['id'])){{$field['id']}}@endif" value="1"
                                       @if($field['value'] == 1) checked @endif>
                                {!! $field['text'] !!} {{--&nbsp;<label class="form-check-label" for="exampleCheck1">{{$field['label']}}</label>--}}
                            </div>

                        @elseif($field['type'] == "radio")
                            @foreach($field['options'] as $value => $label)

                                <div class="form-check form-check-inline">
                                    <input id="{{$name}}_{{$value}}" class="form-check-input  {{$validation_class}}"
                                           type="radio" name="{{$name}}" value="{{$value}}"
                                           @if($field['value'] == $value) checked @endif>
                                    <label class="form-check-label" for="{{$name}}_{{$value}}">{{$label}}</label>
                                </div>
                            @endforeach
                        @elseif($field['type'] == "textarea")
                            <textarea type="text" class="form-control" name="{{$name}}" value="{{$field['value']}}"
                                      placeholder="{{$field['placeholder']}}" rows="3"></textarea>
                        @elseif($field['type'] == "autocomplete")
                            <input type="text" name="{{$name}}" value="{{$field['value']}}" id="search-bar"
                                   class="form-control" aria-describedby="" placeholder="{{$field['placeholder']}}">
                        @elseif($field['type'] == "date")
                            <input type="{{$field['type']}}" name="{{$name}}" value="{{$field['value']}}"
                                   class="date-picker form-control{{$validation_class}}" id="{{$id}}"
                                   aria-describedby="" placeholder="{{$field['placeholder']}}">
                        @else
                            <input type="{{$field['type']}}" name="{{$name}}" value="{{$field['value']}}"
                                   class="form-control{{$validation_class}}" id="{{$id}}" aria-describedby=""
                                   placeholder="{{$field['placeholder']}}">
                        @endif

                        @if(isset($field['text']) && !$field['type'] == "checkbox")
                            <small id="emailHelp"
                                   class="form-text text-muted @if(isset($field['text-class'])) {{$field['text-class']}} @endif">{!! $field['text'] !!}</small>
                        @endif

                        @if(isset($field['password']))
                            <small id="emailHelp"
                                   class="form-text text-muted @if(isset($field['text-class'])) {{$field['text-class']}} @endif">{!! $field['text'] !!}</small>
                        @endif

                        @if(isset($field['date']))
                            <small id="emailHelp"
                                   class="form-text text-muted @if(isset($field['text-class'])) {{$field['text-class']}} @endif">{!! $field['date'] !!}</small>
                        @endif

                        @if ($errors->has($name))
                            {{--This will be displayed if we have parent class on input element of custom-select or form-control etc.--}}
                            <div class="invalid-feedback">
                                {{ $errors->first($name) }}
                            </div>
                        @endif
                        {{--@if ($errors->has($name))
                        <span class="text-muted text-danger">
                                                        <strong>{{ $errors->first($name) }}</strong>
                                                    </span>
                    @endif--}}
                    </div>
</div>