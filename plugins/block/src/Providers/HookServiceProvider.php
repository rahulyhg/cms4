<?php

namespace Botble\Block\Providers;

use Botble\Block\Repositories\Interfaces\BlockInterface;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        add_shortcode('static-block', __('Static Block'), __('Add a custom static block'), [$this, 'render']);
        //shortcode()->setAdminConfig('static-block', view('plugins.block::partials.short-code-admin-config')->render());
    }

    /**
     * @param $shortcode
     * @return null
     * @author Sang Nguyen
     */
    public function render($shortcode)
    {
        $block = app(BlockInterface::class)->getFirstBy([
            'alias' => $shortcode->alias,
            'status' => 1,
        ]);

        $block = apply_filters(BASE_FILTER_BEFORE_GET_SINGLE, $block, app(BlockInterface::class)->getModel(), BLOCK_MODULE_SCREEN_NAME);

        if (empty($block)) {
            return null;
        }

        return $block->content;
    }
}
