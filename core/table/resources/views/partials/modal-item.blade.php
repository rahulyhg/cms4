<div class="modal fade {{ $name }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-{{ $type }}">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="til_img"></i><strong>{{ $title }}</strong></h4>
            </div>

            <div class="modal-body with-padding">
                <div>{!! $content !!}</div>
            </div>

            <div class="modal-footer">
                <button class="pull-left btn btn-warning" data-dismiss="modal">{{ trans('core.table::general.cancel') }}</button>
                <button class="pull-right btn btn-{{ $type }} {{ array_get($action_button_attributes, 'class') }}" {!! Html::attributes(array_except($action_button_attributes, 'class')) !!}>{{ $action_name }}</button>
            </div>
        </div>
    </div>
</div>
<!-- end Modal -->