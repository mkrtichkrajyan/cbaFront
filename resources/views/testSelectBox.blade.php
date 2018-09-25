@extends('layouts.selectbox')

@include('layouts.head.headSelectBox')

<div class="select_inputs">
                <label>Երկրներ</label>
                <div class="">
                    <div class="ui fluid multiple search selection dropdown multiple_select">
                        <input type="hidden" name="countries" value="{{old('countries')}}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Ընտրել ցանկից</div>
                        <div class="menu">
                            @foreach($countries as $country)
                                <div class="item" data-value="{{$country->id}}"><i
                                            class="{{$country->code}} flag"></i>{{$country->name_am}}</div>
                            @endforeach
                        </div>


                    </div>
                </div>
                @if ($errors->has('countries'))
                    <span class="help-block err-field">
                                            <strong>{{ $errors->first('countries') }}</strong>
                                        </span>
                @endif
            </div>

<script>

    $(document).ready(function () {

        if ($(".multiple_select").length > 0) {
            $(".multiple_select").dropdown();
        }
    });
</script>