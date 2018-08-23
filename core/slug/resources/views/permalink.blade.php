<div id="edit-slug-box" @if (empty($value) && !$errors->has($name)) class="hidden" @endif>
    <label class="control-label required" for="current-slug">{{ trans('core.base::forms.permalink') }}:</label>
    <span id="sample-permalink">
        <a class="permalink" target="_blank" href="{{ str_replace('--slug--', $value, $preview) }}">
            <span class="default-slug">{{ $default_slug }}/<span id="editable-post-name">{{ $value }}</span>{{ $ending_url }}</span>
        </a>
    </span>
    ‎<span id="edit-slug-buttons">
        <button type="button" class="btn btn-default" id="change_slug">{{ trans('core.base::forms.edit') }}</button>
        <button type="button" class="save btn btn-default" id="btn-ok">{{ trans('core.base::forms.ok') }}</button>
        <button type="button" class="cancel button-link">{{ trans('core.base::forms.cancel') }}</button>
    </span>
</div>
<input type="hidden" id="current-slug" name="{{ $name }}" value="{{ $value }}">
<div data-url="{{ $url ? $url : route('slug.create') }}" data-view="{{ rtrim(str_replace('--slug--', '', $preview), '/') . '/' }}" id="slug_id" data-id="{{ $id }}"></div>
<input type="hidden" name="slug_id" value="{{ $id }}">