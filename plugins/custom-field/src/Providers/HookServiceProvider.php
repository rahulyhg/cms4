<?php

namespace Botble\CustomField\Providers;

use Assets;
use Auth;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\CustomField\Facades\CustomFieldSupportFacade;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'handle'], 125, 3);
    }

    /**
     * @param string $location
     * @param string $screenName
     * @param \Eloquent $object
     * @throws \Throwable
     */
    public function handle($screenName, $priority, $object = null)
    {
        if ($priority == 'advanced') {
            Assets::addJavascriptDirectly(config('core.base.general.editor.ckeditor.js'));
            Assets::addJavascript(['jquery-ui']);
            Assets::addStylesheetsDirectly('vendor/core/plugins/custom-field/css/custom-field.css');
            Assets::addJavascriptDirectly('vendor/core/plugins/custom-field/js/use-custom-fields.js');

            /**
             * Every models will have these rules by default
             */
            add_custom_fields_rules_to_check([
                'logged_in_user' => Auth::user()->getKey(),
                'logged_in_user_has_role' => app(RoleInterface::class)->pluck('id'),
            ]);

            switch ($screenName) {
                case PAGE_MODULE_SCREEN_NAME:
                    add_custom_fields_rules_to_check([
                        'page_template' => isset($object->template) ? $object->template : '',
                        'page' => isset($object->id) ? $object->id : '',
                        'model_name' => PAGE_MODULE_SCREEN_NAME,
                    ]);
                    break;
            }

            if (is_plugin_active('blog')) {
                switch ($screenName) {
                    case CATEGORY_MODULE_SCREEN_NAME:
                        add_custom_fields_rules_to_check([
                            CATEGORY_MODULE_SCREEN_NAME => isset($object->id) ? $object->id : null,
                            'model_name' => CATEGORY_MODULE_SCREEN_NAME,
                        ]);
                        break;
                    case POST_MODULE_SCREEN_NAME:
                        add_custom_fields_rules_to_check([
                            'model_name' => POST_MODULE_SCREEN_NAME,
                        ]);
                        if ($object) {
                            $relatedCategoryIds = app(PostInterface::class)->getRelatedCategoryIds($object);
                            add_custom_fields_rules_to_check([
                                POST_MODULE_SCREEN_NAME . '.post_with_related_category' => $relatedCategoryIds,
                                POST_MODULE_SCREEN_NAME . '.post_format' => $object->format_type,
                            ]);
                        }
                        break;
                }
            }

            echo $this->render($screenName, isset($object->id) ? $object->id : null);
        }
    }

    /**
     * @param $screenName
     * @param $id
     * @throws \Throwable
     */
    protected function render($screenName, $id)
    {
        $customFieldBoxes = get_custom_field_boxes($screenName, $id);

        if (!$customFieldBoxes) {
            return null;
        }

        CustomFieldSupportFacade::renderAssets();

        return CustomFieldSupportFacade::renderCustomFieldBoxes($customFieldBoxes);
    }
}
