<div class="widget meta-boxes">
    <div class="widget-title">
        <h4><span class="required">{{ trans('core.base::tables.status') }}</span></h4>
    </div>
    <div class="widget-body">
        {!! Form::select(isset($name) ? $name : 'status', isset($values) ? $values : [1 => trans('core.base::system.activated'), 0 => trans('core.base::system.deactivated')], isset($selected) ? $selected : old(isset($name) ? $name : 'status', 1), ['class' => 'form-control select-full']) !!}
    </div>
</div>