@extends('core.base::layouts.master')
@section('content')
    {!! Form::open(['route' => ['settings.edit']]) !!}
    <div class="flexbox-annotated-section">

        <div class="flexbox-annotated-section-annotation">
            <div class="annotated-section-title pd-all-20">
                <h2>{{ trans('core.setting::setting.general.general_block') }}</h2>
            </div>
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{{ trans('core.setting::setting.general.description') }}</p>
            </div>
        </div>

        <div class="flexbox-annotated-section-content">
            <div class="wrapper-content pd-all-20">
                <div class="form-group">
                    <label class="text-title-field"
                           for="site_title">{{ trans('core.setting::setting.general.site_title') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="site_title" id="site_title"
                           value="{{ setting('site_title') }}">
                </div>

                <div class="form-group">
                    <label class="text-title-field"
                           for="admin_title">{{ trans('core.setting::setting.general.admin_title') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="admin_title" id="admin_title"
                           value="{{ setting('admin_title') }}">
                </div>

                <div class="form-group">
                    <label class="text-title-field"
                           for="admin_email">{{ trans('core.setting::setting.general.admin_email') }}</label>
                    <input type="email" class="next-input" name="admin_email" id="admin_email"
                           value="{{ setting('admin_email') }}">
                </div>

            </div>
        </div>

    </div>

    <div class="flexbox-annotated-section">

        <div class="flexbox-annotated-section-annotation">
            <div class="annotated-section-title pd-all-20">
                <h2>{{ trans('core.setting::setting.general.admin_appearance_title') }}</h2>
            </div>
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{{ trans('core.setting::setting.general.admin_appearance_description') }}</p>
            </div>
        </div>

        <div class="flexbox-annotated-section-content">
            <div class="wrapper-content pd-all-20">
                <div class="form-group">
                    <label class="text-title-field"
                           for="enable_multi_language_in_admin">{{ trans('core.setting::setting.general.admin_logo') }}
                    </label>
                    <div class="admin-logo-image-setting">
                        {!! Form::mediaImage('admin_logo', setting('admin_logo', config('core.base.general.logo')), [
                        'allow_thumb' => false,
                    ]) !!}
                    </div>
                </div>

                <div class="form-group">

                    <label class="text-title-field"
                           for="rich_editor">{{ trans('core.setting::setting.general.rich_editor') }}
                    </label>
                    <label class="hrv-label">
                        <input type="radio" name="rich_editor" class="hrv-radio" value="ckeditor"
                               @if (setting('rich_editor', 'ckeditor') == 'ckeditor') checked @endif>{{ __('CKEditor') }}
                    </label>
                    <label class="hrv-label">
                        <input type="radio" name="rich_editor" class="hrv-radio" value="tinymce"
                               @if (setting('rich_editor', 'ckeditor') == 'tinymce') checked @endif>{{ __('TinyMCE') }}
                    </label>
                </div>


                <div class="form-group">

                    <div class="mt5">
                        <input type="hidden" name="show_admin_bar" value="0">
                        <label><input type="checkbox" class="hrv-checkbox" value="1"
                                      @if (setting('show_admin_bar')) checked @endif name="show_admin_bar"> {{ trans('core.setting::setting.general.show_admin_bar') }} </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt5">
                        <input type="hidden" name="enable_change_admin_theme" value="0">
                        <label><input type="checkbox" class="hrv-checkbox" value="1"
                                      @if (setting('enable_change_admin_theme')) checked @endif name="enable_change_admin_theme"> {{ trans('core.setting::setting.general.enable_change_admin_theme') }} </label>
                    </div>
                </div>

                <div class="form-group">

                    <div class="form-group">
                        <div class="mt5">
                            <input type="hidden" name="enable_multi_language_in_admin" value="0">
                            <label><input type="checkbox" class="hrv-checkbox" value="1"
                                          @if (setting('enable_multi_language_in_admin')) checked @endif name="enable_multi_language_in_admin"> {{ trans('core.setting::setting.general.enable_multi_language_in_admin') }} </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flexbox-annotated-section">
        <div class="flexbox-annotated-section-annotation">
            <div class="annotated-section-title pd-all-20">
                <h2>{{ trans('core.setting::setting.general.seo_block') }}</h2>
            </div>
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{{ trans('core.setting::setting.general.seo_block_description') }}</p>
            </div>
        </div>

        <div class="flexbox-annotated-section-content">
            <div class="wrapper-content pd-all-20">
                <div class="form-group">
                    <label class="text-title-field"
                           for="seo_title">{{ trans('core.setting::setting.general.seo_title') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="seo_title" id="seo_title"
                           value="{{ setting('seo_title') }}">
                </div>

                <div class="form-group">
                    <label class="text-title-field"
                           for="seo_description">{{ trans('core.setting::setting.general.seo_description') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="seo_description" id="seo_description"
                           value="{{ setting('seo_description') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="flexbox-annotated-section">
        <div class="flexbox-annotated-section-annotation">
            <div class="annotated-section-title pd-all-20">
                <h2>{{ trans('core.setting::setting.general.webmaster_tools_block') }}</h2>
            </div>
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{{ trans('core.setting::setting.general.webmaster_tools_description') }}</p>
            </div>
        </div>

        <div class="flexbox-annotated-section-content">
            <div class="wrapper-content pd-all-20">
                <div class="form-group">
                    <label class="text-title-field"
                           for="google_site_verification">{{ trans('core.setting::setting.general.google_site_verification') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="google_site_verification"
                           id="google_site_verification" value="{{ setting('google_site_verification') }}">
                </div>

                <div class="form-group">
                    <label class="text-title-field"
                           for="enable_captcha">{{ trans('core.setting::setting.general.enable_captcha') }}
                    </label>
                    <label class="hrv-label"><input type="radio" name="enable_captcha" class="hrv-radio"
                                                    value="1"
                                                    @if (setting('enable_captcha')) checked @endif>{{ trans('core.setting::setting.general.yes') }}
                    </label>
                    <label class="hrv-label"><input type="radio" name="enable_captcha" class="hrv-radio"
                                                    value="0"
                                                    @if (!setting('enable_captcha')) checked @endif>{{ trans('core.setting::setting.general.no') }}
                    </label>
                </div>

            </div>
        </div>
    </div>

    <div class="flexbox-annotated-section">
        <div class="flexbox-annotated-section-annotation">
            <div class="annotated-section-title pd-all-20">
                <h2>{{ trans('core.setting::setting.general.cache_block') }}</h2>
            </div>
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{{ trans('core.setting::setting.general.cache_description') }}</p>
            </div>
        </div>

        <div class="flexbox-annotated-section-content">
            <div class="wrapper-content pd-all-20">

                <div class="form-group">
                    <label class="text-title-field"
                           for="enable_cache">{{ trans('core.setting::setting.general.enable_cache') }}
                    </label>
                    <label class="hrv-label"><input type="radio" name="enable_cache" class="hrv-radio"
                                                    value="1"
                                                    @if (setting('enable_cache')) checked @endif>{{ trans('core.setting::setting.general.yes') }}
                    </label>
                    <label class="hrv-label"><input type="radio" name="enable_cache" class="hrv-radio"
                                                    value="0"
                                                    @if (!setting('enable_cache')) checked @endif>{{ trans('core.setting::setting.general.no') }}
                    </label>
                </div>

                <div class="form-group mb0 row">
                    <div class="col-sm-6 p-none-l">
                        <label class="text-title-field"
                               for="cache_time">{{ trans('core.setting::setting.general.cache_time') }}</label>
                        <input type="number" class="next-input" name="cache_time" id="cache_time"
                               value="{{ setting('cache_time') }}">
                    </div>
                    <div class="col-sm-6 p-none-l">
                        <label class="text-title-field"
                               for="cache_time_site_map">{{ trans('core.setting::setting.general.cache_time_site_map') }}</label>
                        <input type="number" class="next-input" name="cache_time_site_map" id="cache_time_site_map"
                               value="{{ setting('cache_time_site_map') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! apply_filters(BASE_FILTER_AFTER_SETTING_CONTENT, null) !!}

    <div class="flexbox-annotated-section" style="border: none">
        <div class="flexbox-annotated-section-annotation">
            &nbsp;
        </div>
        <div class="flexbox-annotated-section-content">
            <button class="btn btn-info" type="submit">{{ trans('core.setting::setting.save_settings') }}</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection