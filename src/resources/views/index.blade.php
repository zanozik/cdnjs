<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>@lang('cdnjs.title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {!! cdnjs(['bootstrap-css','select2-css']) !!}
    {{--
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
     --}}
    <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
</head>
<body>
<style>
    body {
        font-family: 'Arimo', sans-serif;
    }
    th{
        text-align: center;
    }
    body {
        padding: 3em;
    }
    .form-control{
        height: 28px;
    }

    .btn-group {
        display: flex;
        white-space: nowrap;
    }

    .btn-group .btn {
        float: none;
    }

    .doc-type {
        position: relative;
        margin: 2px 5px;
    }
    .doc-type::before {
        position: absolute;
        width: 26px;
        height: 32px;
        left: 0;
        top: -7px;
        content: '';
        border: solid 2px #337ab7;
    }
    .doc-type::after {
        content: 'file';
        content: attr(filetype);
        left: -7px;
        padding: 0px 2px;
        text-align: right;
        line-height: 1.3;
        position: absolute;
        background-color: #000;
        color: #fff;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        top: 7px;

    }
    .doc-type .corner {
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 11px 0 0 11px;
        border-color: white transparent transparent #337ab7;
        position: absolute;
        top: -7px;
        left: 15px;
    }
    .modal{
        position: relative;
    }
    .modal.in{
        position: fixed;
    }
    .spinner {
        display: none;
        text-align: center;
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 70px;
        overflow: visible;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }
    .spinner:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }
    .spinner > div {
        width: 18px;
        height: 18px;
        background-color: #333;

        border-radius: 100%;
        display: inline-block;
        -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        animation: sk-bouncedelay 1.4s infinite ease-in-out both;
    }

    .spinner .bounce1 {
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }

    .spinner .bounce2 {
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }

    @-webkit-keyframes sk-bouncedelay {
        0%, 80%, 100% { -webkit-transform: scale(0) }
        40% { -webkit-transform: scale(1.0) }
    }

    @keyframes sk-bouncedelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        } 40% {
              -webkit-transform: scale(1.0);
              transform: scale(1.0);
          }
    }
</style>
<div class="container">
    <div class="row">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><span class="navbar-brand">@lang('cdnjs.title')</span></div>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <button data-path="{{route('asset.create')}}" class="btn btn-sm" data-toggle="modal"
                                data-target="#modal">@lang('cdnjs.add_new')</button>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>@lang('cdnjs.type')</th>
                    <th>@lang('cdnjs.name')</th>
                    <th>@lang('cdnjs.library')</th>
                    <th>@lang('cdnjs.latest_version')</th>
                    <th>@lang('cdnjs.new_version')</th>
                    <th>@lang('cdnjs.current_version')</th>
                    <th>@lang('cdnjs.file')</th>
                    <th>@lang('cdnjs.version_check')</th>
                    <th>@lang('cdnjs.autoupdate')</th>
                    <th>@lang('cdnjs.action')</th>
                </tr>
                </thead>
                <tbody>
                    @if($assets->count())
                        @foreach($assets as $asset)
                            <tr class="{{$asset->new_version ? 'success' : ''}} {{$asset->testing ? 'warning' : ''}}">
                                <td><div class="doc-type" filetype="{{$asset->type}}"><span class="corner"></span></div></td>
                                <td>{{$asset->name}}</td>
                                <td>{{$asset->library}}</td>
                                <td>{{$asset->latest_version}}</td>
                                <td>
                                    {{$asset->new_version}}<br>
                                    @if($asset->new_version)
                                        <form method="post" action="{{route('asset.update', [$asset->id])}}"
                                              class="form">
                                            {{csrf_field()}}{{method_field('PATCH')}}
                                            <div class="btn-group" role="group" aria-label="...">
                                                <button type="submit" name="testing" value="{{$asset->testing ? 0 : 1}}"
                                                        class="btn btn-xs btn-warning">{{$asset->testing ? trans('cdnjs.undo') : trans('cdnjs.test')}}</button>
                                                <button type="submit" name="current_version"
                                                        value="{{$asset->new_version}}"
                                                        class="btn btn-xs btn-success">@lang('cdnjs.update')</button>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                                <td>{{$asset->current_version}}</td>
                                <td><a href="{{config('cdnjs.url.ajax')}}{{$asset->library}}/{{$asset->current_version}}/{{$asset->file}}">{{$asset->file}}</a></td>
                                <td>{{trans('cdnjs.masks.' . $asset->version_mask_check)}}</td>
                                <td>{{trans('cdnjs.masks.' . $asset->version_mask_autoupdate)}}</td>
                                <td>

                                    <form method="post" action="{{route('asset.delete', [$asset->id])}}" class="form">
                                        {{csrf_field()}}{{method_field('DELETE')}}
                                        <div class="btn-group" role="group" aria-label="...">
                                            <button type="button" data-path="{{route('asset.edit', [$asset->id])}}"
                                                    class="btn btn-xs btn-primary" data-toggle="modal"
                                                    data-target="#modal">@lang('cdnjs.edit')</button>
                                            <button type="submit" class="btn btn-xs btn-danger">@lang('cdnjs.delete')</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">@lang('cdnjs.no_assets')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal" class="modal fade" role="dialog"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>
{!! cdnjs(['jquery','bootstrap-js','select2-js']) !!}
{{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
 --}}
<script>
    $(document).ready(function() {
        $('#modal').on('show.bs.modal', function (e) {
            $.getJSON($(e.relatedTarget).data('path'), function(data) {
                $('#modal').html(data.view);
            });
        });
    });
</script>
</body>
</html>