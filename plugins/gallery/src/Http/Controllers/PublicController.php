<?php

namespace Botble\Gallery\Http\Controllers;

use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Gallery;
use Illuminate\Routing\Controller;
use Theme;

class PublicController extends Controller
{

    /**
     * @var GalleryInterface
     */
    protected $galleryRepository;

    /**
     * PublicController constructor.
     * @param GalleryInterface $galleryRepository
     * @author Sang Nguyen
     */
    public function __construct(GalleryInterface $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getGalleries()
    {
        Gallery::registerAssets();
        $galleries = $this->galleryRepository->getAll();
        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Galleries'), route('public.galleries'));
        return Theme::scope('galleries', compact('galleries'), 'plugins.gallery::themes.galleries')->render();
    }
}
