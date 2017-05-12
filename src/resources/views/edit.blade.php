<div class="modal-dialog modal-lg" role="document">
    <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
    <form method="post" action="{{route('asset.update', [$asset->id])}}" class="modal-content">
        @if($asset['exists']) {{method_field('PATCH')}} @endif
        {{csrf_field()}}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('cdnjs.new_library')</h4>
            <div class="repo"></div>
            <div class="description"></div>
            <div class="author"></div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="library">@lang('cdnjs.library')</label>
                    <select id="library" name="library" required>
                        <option>{{$asset->library}}</option>
                    </select>
                </div>
                <div class="form-group col-sm-2 col-xs-12">
                    <label for="latest_version">@lang('cdnjs.latest_version')</label>
                    <input id="latest_version" name="latest_version" value="{{$asset->latest_version}}" class="form-control" readonly>
                </div>
                <div class="form-group col-sm-2 col-xs-12">
                    <label for="current_version">@lang('cdnjs.current_version')</label>
                    <select id="current_version" name="current_version" class="select2" required>
                        <option disabled>@lang('cdnjs.select_library')</option>
                    </select>
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="file">@lang('cdnjs.file')</label>
                    <select id="file" name="file" class="select2" required>
                        <option disabled>@lang('cdnjs.select_library')</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="name">@lang('cdnjs.name')</label>
                    <input {{$asset->name ? 'readonly' : 'id=name'}} name="name" value="{{$asset->name}}" class="form-control" required>
                    @if(!$asset->exists)
                        <p class="help-block">@lang('cdnjs.name_helpblock')</p>
                    @endif
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="version_mask_check">@lang('cdnjs.check')</label>
                    <select id="version_mask_check" name="version_mask_check" class="select2"></select>
                    <p class="help-block">@lang('cdnjs.check_helpblock')</p>
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="version_mask_autoupdate">@lang('cdnjs.autoupdate')</label>
                    <select id="version_mask_autoupdate" name="version_mask_autoupdate" class="select2"></select>
                    <p class="help-block">@lang('cdnjs.autoupdate_helpblock')</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('cdnjs.close')</button>
            <button type="submit" class="btn btn-primary">@lang('cdnjs.submit')</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){

        var options = [
                @foreach(trans('cdnjs.masks') as $key=>$value)
            {
                id: '{{$key}}', text: '{{$value}}'
            },
            @endforeach
        ];

        var mask_check = parseInt({{$asset->version_mask_check}}) || 0;
        var mask_autoupdate = parseInt({{$asset->version_mask_autoupdate}}) || 0;

        $('.select2').select2({
            width: '100%'
        });

        $('#version_mask_check').select2({
            width: '100%',
            data: options
        }).change(function () {
            mask_check = $(this).val();
            $('#version_mask_autoupdate').html('').select2({
                width: '100%',
                data: options.filter(function (option) {
                    return (mask_check > 0 && mask_check <= option.id) || option.id == 0
                })
            }).val(mask_check == 0 || mask_autoupdate < mask_check ? 0 : mask_autoupdate).trigger('change');
        }).val(mask_check).trigger('change');


        $('#library').select2({
            width: '100%',
            placeholder: "Choose a library",
            ajax: {
                url: function (params) {
                    return '{{config('cdnjs.url.api')}}?search=' + params.term;
                },
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function(item){
                            return {id: item.name, text: item.name};
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2,

        }).on('change', function (librarySelected) {
            $('.spinner').show();
            $.getJSON('{{config('cdnjs.url.api')}}' +  librarySelected.target.value, function(data){
                $('.modal-title').html('<a href="' + data.homepage + '">' + data.name + '</a>');
                $('.description').html(data.description);
                $('.repo').html('<a href="' + data.repository.url + '">' + data.repository.type + '</a> | ' + data.license);
                $('#latest_version').val(data.version);
                if(typeof data.author == 'object') $('.author').html('&copy; <a href="' + (data.author.email ? 'mailto:' +data.author.email : data.author.url) + '">' + data.author.name + '</a>');

                var versions = [];
                var files = {};
                $.each(data.assets, function (i, asset) {
                    versions.push(asset.version);
                    files[asset.version] = asset.files;
                });

                $('#current_version').html('').select2({
                    width: '100%',
                    data: versions

                }).on('change', function (versionSelected) {
                    $('#file').html('').select2({
                        width: '100%',
                        data: files[versionSelected.target.value]

                    }).on('change', function () {
                        var text = $('#file').val() ? ($('#library').val() + '/' +  $('#file').val()).replace(/[\W_]+/g, "-") : "";
                        $('#name').val(text);
                        $('.spinner').hide();
                    }).val("{{$asset->file}}").trigger('change');

                }).val("{{$asset->current_version}}").trigger('change');
            });
        });
        @if($asset->library)
            $('#library').trigger('change');
        @endif
    });
</script>