let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .sass('./core/base/resources/assets/sass/base/themes/black.scss', 'public/vendor/core/css/themes')
    .sass('./core/base/resources/assets/sass/base/themes/default.scss', 'public/vendor/core/css/themes')
    .copy('./public/vendor/core/css', 'core/base/public/css')

    .sass('./core/base/resources/assets/sass/core.scss', 'public/vendor/core/css')
    .copy('./public/vendor/core/css/core.css', 'core/base/public/css')
    .sass('./core/base/resources/assets/sass/custom/admin-bar.scss', 'public/vendor/core/css')
    .copy('./public/vendor/core/css/admin-bar.css', 'core/base/public/css')
    .sass('./core/base/resources/assets/sass/custom/system-info.scss', 'public/vendor/core/css')
    .copy('./public/vendor/core/css/system-info.css', 'core/base/public/css')

    .scripts(
        [
            './core/base/resources/assets/js/base/app.js',
            './core/base/resources/assets/js/base/layout.js',
            './core/base/resources/assets/js/script.js',
            './core/base/resources/assets/js/csrf.js'
        ], 'public/vendor/core/js/core.js')
    .copy('./public/vendor/core/js/core.js', 'core/base/public/js');

// Modules Core
mix.js('./core/base/resources/assets/js/app_modules/editor.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/editor.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/datatables.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/datatables.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/plugin.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/plugin.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/cache.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/cache.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/tags.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/tags.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/system-info.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/system-info.js', 'core/base/public/js/app_modules')
    
    .js('./core/setting/resources/assets/js/app_modules/setting.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/setting.js', 'core/setting/public/js/app_modules')

    .js('./core/table/resources/assets/js/app_modules/table.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/table.js', 'core/table/public/js/app_modules')
    .js('./core/table/resources/assets/js/app_modules/filter.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/filter.js', 'core/table/public/js/app_modules')
    .sass('./core/table/resources/assets/sass/table.scss', 'public/vendor/core/css/components')
    .copy('./public/vendor/core/css/components/table.css', 'core/table/public/css/components')

    .scripts(['./core/dashboard/resources/assets/js/app_modules/dashboard.js'], 'public/vendor/core/js/app_modules/dashboard.js')
    .copy('./public/vendor/core/js/app_modules/dashboard.js', 'core/dashboard/public/js/app_modules')

    .scripts(['./core/acl/resources/assets/js/app_modules/profile.js'], 'public/vendor/core/js/app_modules/profile.js')
    .copy('./public/vendor/core/js/app_modules/profile.js', 'core/acl/public/js/app_modules')
    .js('./core/acl/resources/assets/js/app_modules/login.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/login.js', 'core/acl/public/js/app_modules')
    .js('./core/acl/resources/assets/js/app_modules/role.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/role.js', 'core/acl/public/js/app_modules')

    .js('./core/slug/resources/assets/js/app_modules/slug.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/slug.js', 'core/slug/public/js/app_modules')

    .js('./core/menu/resources/assets/js/app_modules/menu.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/menu.js', 'core/menu/public/js/app_modules')

    .js('./core/seo-helper/resources/assets/js/app_modules/seo-helper.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/seo-helper.js', 'core/seo-helper/public/js/app_modules')

    .js('./core/widget/resources/assets/js/app_modules/widget.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/widget.js', 'core/widget/public/js/app_modules')

    .js('./core/theme/resources/assets/js/app_modules/custom-css.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/custom-css.js', 'core/theme/public/js/app_modules')
    .js('./core/theme/resources/assets/js/app_modules/theme-options.js', 'public/vendor/core/js/app_modules')
    .copy('./public/vendor/core/js/app_modules/theme-options.js', 'core/theme/public/js/app_modules')
    .sass('./core/theme/resources/assets/sass/custom-css.scss', 'public/vendor/core/css')
    .copy('./public/vendor/core/css/custom-css.css', 'core/theme/public/css');

// Media
mix
    .sass('./core/media/resources/assets/sass/media.scss', 'public/vendor/core/media/css/media.css')
    .copy('./public/vendor/core/media/css/media.css', 'core/media/public/assets/css')
    .js('./core/media/resources/assets/js/media.js', 'public/vendor/core/media/js')
    .copy('./public/vendor/core/media/js/media.js', 'core/media/public/assets/js')
    .js('./core/media/resources/assets/js/jquery.addMedia.js', 'public/vendor/core/media/js')
    .copy('./public/vendor/core/media/js/jquery.addMedia.js', 'core/media/public/assets/js')
    .js('./core/media/resources/assets/js/integrate.js', 'public/vendor/core/media/js')
    .copy('./public/vendor/core/media/js/integrate.js', 'core/media/public/assets/js');

// JS Validation
mix.scripts('./vendor/proengsoft/laravel-jsvalidation/public/js/jsvalidation.js', 'public/vendor/core/js/app_modules/form-validation.js')
    .copy('./public/vendor/core/js/app_modules/form-validation.js', 'core/base/public/js/app_modules');

// Translation
mix.js('./plugins/translation/resources/assets/js/translation.js', 'public/vendor/core/plugins/translation/js')
    .copy('./public/vendor/core/plugins/translation/js/translation.js', 'plugins/translation/public/js')
    .sass('./plugins/translation/resources/assets/sass/translation.scss', 'public/vendor/core/plugins/translation/css/translation.css')
    .copy('./public/vendor/core/plugins/translation/css/translation.css', 'plugins/translation/public/css');

// Backup
mix.js('./plugins/backup/resources/assets/js/backup.js', 'public/vendor/core/plugins/backup/js')
    .copy('./public/vendor/core/plugins/backup/js/backup.js', 'plugins/backup/public/js')
    .sass('./plugins/backup/resources/assets/sass/backup.scss', 'public/vendor/core/plugins/backup/css/backup.css')
    .copy('./public/vendor/core/plugins/backup/css/backup.css', 'plugins/backup/public/css');

// Language
mix
    .scripts(['./plugins/language/resources/assets/js/language.js'], 'public/vendor/core/plugins/language/js/language.js')
    .copy('./public/vendor/core/plugins/language/js/language.js', 'plugins/language/public/js')
    .scripts(['./plugins/language/resources/assets/js/language-global.js'], 'public/vendor/core/plugins/language/js/language-global.js')
    .copy('./public/vendor/core/plugins/language/js/language-global.js', 'plugins/language/public/js')
    .scripts(['./plugins/language/resources/assets/js/language-public.js'], 'public/vendor/core/plugins/language/js/language-public.js')
    .copy('./public/vendor/core/plugins/language/js/language-public.js', 'plugins/language/public/js')
    .sass('./plugins/language/resources/assets/sass/language.scss', 'public/vendor/core/plugins/language/css/language.css')
    .copy('./public/vendor/core/plugins/language/css/language.css', 'plugins/language/public/css')
    .sass('./plugins/language/resources/assets/sass/language-public.scss', 'public/vendor/core/plugins/language/css/language-public.css')
    .copy('./public/vendor/core/plugins/language/css/language-public.css', 'plugins/language/public/css')

// Custom fields
mix
    .sass('./plugins/custom-field/resources/assets/sass/edit-field-group.scss', 'public/vendor/core/plugins/custom-field/css')
    .copy('./public/vendor/core/plugins/custom-field/css/edit-field-group.css', 'plugins/custom-field/public/css')
    .sass('./plugins/custom-field/resources/assets/sass/custom-field.scss', 'public/vendor/core/plugins/custom-field/css')
    .copy('./public/vendor/core/plugins/custom-field/css/custom-field.css', 'plugins/custom-field/public/css')
    .js('./plugins/custom-field/resources/assets/js/edit-field-group.js', 'public/vendor/core/plugins/custom-field/js')
    .copy('./public/vendor/core/plugins/custom-field/js/edit-field-group.js', 'plugins/custom-field/public/js')
    .js('./plugins/custom-field/resources/assets/js/use-custom-fields.js', 'public/vendor/core/plugins/custom-field/js')
    .copy('./public/vendor/core/plugins/custom-field/js/use-custom-fields.js', 'plugins/custom-field/public/js')
    .js('./plugins/custom-field/resources/assets/js/import-field-group.js', 'public/vendor/core/plugins/custom-field/js')
    .copy('./public/vendor/core/plugins/custom-field/js/import-field-group.js', 'plugins/custom-field/public/js');

// Gallery
mix
    .sass('./plugins/gallery/resources/assets/sass/gallery.scss', 'public/vendor/core/plugins/gallery/css/gallery.css')
    .copy('./public/vendor/core/plugins/gallery/css/gallery.css', 'plugins/gallery/public/css')

    .sass('./plugins/gallery/resources/assets/sass/object-gallery.scss', 'public/vendor/core/plugins/gallery/css/object-gallery.css')
    .copy('./public/vendor/core/plugins/gallery/css/object-gallery.css', 'plugins/gallery/public/css')

    .sass('./plugins/gallery/resources/assets/sass/admin-gallery.scss', 'public/vendor/core/plugins/gallery/css/admin-gallery.css')
    .copy('./public/vendor/core/plugins/gallery/css/admin-gallery.css', 'plugins/gallery/public/css')

    .scripts('./plugins/gallery/resources/assets/js/gallery.js', 'public/vendor/core/plugins/gallery/js/gallery.js')
    .copy('./public/vendor/core/plugins/gallery/js/gallery.js', 'plugins/gallery/public/js')

    .scripts('./plugins/gallery/resources/assets/js/object-gallery.js', 'public/vendor/core/plugins/gallery/js/object-gallery.js')
    .copy('./public/vendor/core/plugins/gallery/js/object-gallery.js', 'plugins/gallery/public/js');


// Log Viewer
mix
    .sass('./plugins/log-viewer/resources/assets/sass/log-viewer.scss', 'public/vendor/core/plugins/log-viewer/css/log-viewer.css')
    .copy('./public/vendor/core/plugins/log-viewer/css/log-viewer.css', 'plugins/log-viewer/public/css');

mix
    .js('./plugins/member/resources/assets/js/member-admin.js', 'public/vendor/core/plugins/member/js')
    .copy('./public/vendor/core/plugins/member/js/member-admin.js', 'plugins/member/public/js')
    .sass('./plugins/member/resources/assets/sass/member.scss', 'public/vendor/core/plugins/member/css')
    .copy('./public/vendor/core/plugins/member/css/member.css', 'plugins/member/public/css');