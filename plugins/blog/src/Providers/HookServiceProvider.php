<?php

namespace Botble\Blog\Providers;

use Botble\Base\Supports\Helper;
use Botble\SeoHelper\SeoOpenGraph;
use Eloquent;
use Illuminate\Support\ServiceProvider;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Menu;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use Auth;
use SeoHelper;
use Theme;

class HookServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function boot()
    {
        add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 2);
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 21, 1);
        add_filter(DASHBOARD_FILTER_TOP_BLOCKS, [$this, 'addStatsWidgets'], 13, 1);
        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 2, 1);

        admin_bar()->registerLink('Post', route('posts.create'), 'add-new');

        add_shortcode('blog-posts', __('Blog posts'), __('Add blog posts'), [$this, 'renderBlogPosts']);
        shortcode()->setAdminConfig('blog-posts', view('plugins.blog::partials.posts-short-code-admin-config')->render());
    }

    /**
     * Register sidebar options in menu
     * @throws \Throwable
     */
    public function registerMenuOptions()
    {
        $categories = Menu::generateSelect([
            'model' => app(CategoryInterface::class)->getModel(),
            'screen' => CATEGORY_MODULE_SCREEN_NAME,
            'theme' => false,
            'options' => [
                'class' => 'list-item',
            ],
        ]);
        echo view('plugins.blog::categories.partials.menu-options', compact('categories'));

        $tags = Menu::generateSelect([
            'model' => app(TagInterface::class)->getModel(),
            'screen' => TAG_MODULE_SCREEN_NAME,
            'theme' => false,
            'options' => [
                'class' => 'list-item',
            ]
        ]);
        echo view('plugins.blog::tags.partials.menu-options', compact('tags'));
    }

    /**
     * @param $widgets
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function registerDashboardWidgets($widgets)
    {
        $widget = app(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_posts_recent']);
        $widget_setting = app(DashboardWidgetSettingInterface::class)->getFirstBy([
            'widget_id' => $widget->id,
            'user_id' => Auth::user()->getKey(),
        ], ['status', 'order']);

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = view('plugins.blog::posts.widgets.base', compact('widget', 'widget_setting'))->render();
        } else {
            $widgets[$widget_setting->order] = view('plugins.blog::posts.widgets.base', compact('widget', 'widget_setting'))->render();
        }
        return $widgets;
    }

    /**
     * @param $widgets
     * @return string
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function addStatsWidgets($widgets)
    {
        $posts = app(PostInterface::class)->count(['status' => 1]);
        $categories = app(CategoryInterface::class)->count(['status' => 1]);

        $widgets = $widgets . view('plugins.blog::posts.widgets.stats', compact('posts'))->render();
        $widgets = $widgets . view('plugins.blog::categories.widgets.stats', compact('categories'))->render();

        return $widgets;
    }

    /**
     * @param $slug
     * @return array|Eloquent
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handleSingleView($slug)
    {
        if ($slug instanceof Eloquent) {
            $data = [];
            switch ($slug->reference) {
                case POST_MODULE_SCREEN_NAME:
                    $post = app(PostInterface::class)->findById($slug->reference_id);
                    $post = apply_filters(BASE_FILTER_BEFORE_GET_SINGLE, $post, app(PostInterface::class)->getModel(), POST_MODULE_SCREEN_NAME);
                    if (!empty($post)) {
                        Helper::handleViewCount($post, 'viewed_post');

                        SeoHelper::setTitle($post->name)->setDescription($post->description);

                        $meta = new SeoOpenGraph();
                        if ($post->image) {
                            $meta->setImage(url($post->image));
                        }
                        $meta->setDescription($post->description);
                        $meta->setUrl(route('public.single', $slug->key));
                        $meta->setTitle($post->name);
                        $meta->setType('article');

                        SeoHelper::setSeoOpenGraph($meta);

                        admin_bar()->registerLink(trans('plugins.blog::posts.edit_this_post'), route('posts.edit', $post->id));

                        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add($post->name, route('public.single', $slug->key));

                        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, POST_MODULE_SCREEN_NAME, $post);

                        $data = [
                            'view' => 'post',
                            'default_view' => 'plugins.blog::themes.post',
                            'data' => compact('post'),
                        ];
                    }
                    break;
                case CATEGORY_MODULE_SCREEN_NAME:
                    $category = app(CategoryInterface::class)->findById($slug->reference_id);
                    $category = apply_filters(BASE_FILTER_BEFORE_GET_SINGLE, $category, app(CategoryInterface::class)->getModel(), CATEGORY_MODULE_SCREEN_NAME);
                    if (!empty($category)) {
                        SeoHelper::setTitle($category->name)->setDescription($category->description);

                        $meta = new SeoOpenGraph();
                        if ($category->image) {
                            $meta->setImage(url($category->image));
                        }
                        $meta->setDescription($category->description);
                        $meta->setUrl(route('public.single', $slug->key));
                        $meta->setTitle($category->name);
                        $meta->setType('article');

                        SeoHelper::setSeoOpenGraph($meta);

                        admin_bar()->registerLink(trans('plugins.blog::categories.edit_this_category'), route('categories.edit', $category->id));

                        $allRelatedCategoryIds = array_unique(array_merge(app(CategoryInterface::class)->getAllRelatedChildrenIds($category), [$category->id]));

                        $posts = app(PostInterface::class)->getByCategory($allRelatedCategoryIds, 12);

                        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add($category->name, route('public.single', $slug->key));

                        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CATEGORY_MODULE_SCREEN_NAME, $category);

                        return [
                            'view' => 'category',
                            'default_view' => 'plugins.blog::themes.category',
                            'data' => compact('category', 'posts'),
                        ];
                    }
                    break;
                case TAG_MODULE_SCREEN_NAME:
                    $tag = app(TagInterface::class)->findById($slug->reference_id);
                    $tag = apply_filters(BASE_FILTER_BEFORE_GET_SINGLE, $tag, app(TagInterface::class)->getModel(), TAG_MODULE_SCREEN_NAME);

                    if (!$tag) {
                        return abort(404);
                    }

                    SeoHelper::setTitle($tag->name)->setDescription($tag->description);

                    $meta = new SeoOpenGraph();
                    $meta->setDescription($tag->description);
                    $meta->setUrl(route('public.single', $slug->key));
                    $meta->setTitle($tag->name);
                    $meta->setType('article');

                    admin_bar()->registerLink(trans('plugins.blog::tags.edit_this_tag'), route('tags.edit', $tag->id));

                    $posts = get_posts_by_tag($tag->id);

                    Theme::breadcrumb()->add(__('Home'), route('public.index'))->add($tag->name, route('public.single', $slug->key));

                    do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, TAG_MODULE_SCREEN_NAME, $tag);

                    $data = [
                        'view' => 'tag',
                        'default_view' => 'plugins.blog::themes.tag',
                        'data' => compact('tag', 'posts'),
                    ];
                    break;
            }
            if (!empty($data)) {
                return $data;
            }
        }

        return $slug;
    }

    /**
     * @param $shortcode
     * @return \Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function renderBlogPosts($shortcode)
    {
        $query = app(PostInterface::class)
            ->getModel()
            ->select('posts.*')
            ->where(['posts.status' => 1]);

        $posts = app(PostInterface::class)->applyBeforeExecuteQuery($query, POST_MODULE_SCREEN_NAME)->paginate($shortcode->paginate ?? 12);

        $view = 'plugins.blog::themes.templates.posts';
        $theme_view = 'theme.' . setting('theme') . '::views.templates.posts';
        if (view()->exists($theme_view)) {
            $view = $theme_view;
        }
        return view($view, compact('posts'))->render();
    }
}
