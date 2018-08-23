<div class="modal-box-container">
    {!! Form::open(['url' => route('api.clients.delete.post', $client->id)]) !!}
        {!! method_field('DELETE') !!}
        <div class="modal-title">
            <i class="til_img"></i> <strong>{{ trans('core.acl::api.confirm_delete_title', ['name' => $client->name]) }}</strong>
        </div>
        <div class="modal-body modal-xs">
            <p>
                {{ trans('core.acl::api.confirm_delete_description', ['name' => $client->name]) }}
            </p>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-warning pull-left" data-fancybox-close>{{ trans('core.acl::api.cancel_delete') }}</a>
            <button type="submit" class="btn btn-danger pull-right">{{ trans('core.acl::api.continue_delete') }}</button>
        </div>
    {!! Form::close() !!}
</div>