<div class="modal-dialog modal-lg" role="document">
    <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
    <form method="post" action="{{route('asset.update', [$asset->id])}}" class="modal-content">
        @if($asset['exists']) {{method_field('PATCH')}} @endif
        {{csrf_field()}}
        <input type="hidden" name="new_version">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">New library</h4>
            <div class="repo"></div>
            <div class="description"></div>
            <div class="author"></div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="library">library</label>
                    <select id="library" name="library" required>
                        <option>{{$asset->library}}</option>
                    </select>
                </div>
                <div class="form-group col-sm-2 col-xs-12">
                    <label for="latest_version">latest version</label>
                    <input id="latest_version" name="latest_version" value="{{$asset->latest_version}}" class="form-control" readonly>
                </div>
                <div class="form-group col-sm-2 col-xs-12">
                    <label for="current_version">current version</label>
                    <select id="current_version" name="current_version" class="select2" required>
                        <option disabled>First, select a library</option>
                    </select>
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="file">file</label>
                    <select id="file" name="file" class="select2" required>
                        <option disabled>First, select a library</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="name">name</label>
                    <input {{$asset->name ? 'readonly' : 'id=name'}} name="name" value="{{$asset->name}}" class="form-control" required>
                    <p class="help-block">Feel free to change this to whatever you want, just make sure it's unique!</p>
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="version_mask_check">check</label>
                    <select id="version_mask_check" name="version_mask_check" class="select2">
                        @foreach($masks as $key=>$value)
                            <option value="{{$key}}" {{$asset->version_mask_check == $key ? 'selected' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                    <p class="help-block">We can periodically check for a newer version of this library and inform you if it changed inside the version scope you define here.</p>
                </div>
                <div class="form-group col-sm-4 col-xs-12">
                    <label for="version_mask_autoupdate">autoupdate</label>
                    <select id="version_mask_autoupdate" name="version_mask_autoupdate" class="select2">
                        @foreach($masks as $key=>$value)
                            <option value="{{$key}}" {{$asset->version_mask_autoupdate == $key ? 'selected' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                    <p class="help-block">We can autoupdate to a newer version when we found one, inside the version scope you define here.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){

        $('.select2').select2({
            width: '100%'
        });

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