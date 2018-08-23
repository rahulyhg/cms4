<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction or 'horizontal' }}">
    <div class="widget-title">
        <h4>
            <span>{{ __('Actions') }}</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="btn-set">
            <a href="{{ route('roles.list') }}" class="btn btn-warning" id="cancelButton">{{ trans('core.acl::permissions.cancel') }}</a>
            <input type="reset" value="{{ trans('core.acl::permissions.reset') }}" class="btn btn-default">
            @if ($role)
                <a href="{{ route('roles.duplicate', [$role->id]) }}" class="btn btn-primary">{{ trans('core.acl::permissions.duplicate') }}</a>
            @endif
            <input type="submit" value="{{ trans('core.acl::permissions.save') }}" class="btn btn-success">
        </div>
    </div>
</div>
<div id="waypoint"></div>
<div class="form-actions form-actions-fixed-top hidden">
    {!! AdminBreadcrumb::render() !!}
    <div class="btn-set">
        <a href="{{ route('roles.list') }}" class="btn btn-warning" id="cancelButton">{{ trans('core.acl::permissions.cancel') }}</a>
        <input type="reset" value="{{ trans('core.acl::permissions.reset') }}" class="btn btn-default">
        @if ($role)
            <a href="{{ route('roles.duplicate', [$role->id]) }}" class="btn btn-primary">{{ trans('core.acl::permissions.duplicate') }}</a>
        @endif
        <input type="submit" value="{{ trans('core.acl::permissions.save') }}" class="btn btn-success">
    </div>
</div>
