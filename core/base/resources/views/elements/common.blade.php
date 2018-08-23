<script type="text/javascript">

    var Botble = Botble || {};

    Botble.variables = {
        youtube_api_key: '{{ env('YOUTUBE_DATA_API_KEY') }}'
    };

    Botble.routes = {
        home: '{{ url('/') }}',
        admin: '{{ route('dashboard.index') }}',
        media: '{{ route('media.popup') }}',
        media_upload_from_editor: '{{ route('media.files.upload.from.editor') }}',
        change_plugin_status: '{{ route('plugins.change.status') }}'
    };

    Botble.languages = {
        'tables': {!! json_encode(trans('core.base::tables'), JSON_HEX_APOS) !!},
        'notices_msg': {!! json_encode(trans('core.base::notices'), JSON_HEX_APOS) !!},
        'pagination': {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
        'system': {
            'character_remain': '{{ trans('core.base::forms.character_remain') }}'
        }
    };

</script>

@if (session()->has('success_msg') || session()->has('error_msg') || isset($errors) || isset($error_msg))
    <script type="text/javascript">
        $(document).ready(function () {

            @if (session()->has('success_msg'))
                Botble.showNotice('success', '{{ session('success_msg') }}');
            @endif
            @if (session()->has('error_msg'))
                Botble.showNotice('error', '{{ session('error_msg') }}');
            @endif
            @if (isset($error_msg))
                Botble.showNotice('error', '{{ $error_msg }}');
            @endif
            @if (isset($errors))
                @foreach ($errors->all() as $error)
                   Botble.showNotice('error', '{{ $error }}');
                @endforeach
            @endif
        });
    </script>
@endif

{!! Form::modalAction('delete-crud-modal', trans('core.base::tables.confirm_delete'), 'danger',  trans('core.base::tables.confirm_delete_msg'), 'delete-crud-entry', trans('core.base::tables.delete')) !!}
{!! Form::modalAction('delete-many-modal', trans('core.base::tables.confirm_delete'), 'danger',  trans('core.base::tables.confirm_delete_msg'), 'delete-many-entry', trans('core.base::tables.delete')) !!}
