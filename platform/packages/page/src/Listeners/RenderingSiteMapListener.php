<?php

namespace Botble\Page\Listeners;

use Botble\Page\Repositories\Interfaces\PageInterface;
use SiteMapManager;

class RenderingSiteMapListener
{
    protected PageInterface $pageRepository;

    public function __construct(PageInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function handle(): void
    {
        $pages = $this->pageRepository->getDataSiteMap();

        foreach ($pages as $page) {
            SiteMapManager::add($page->url, $page->updated_at, '0.8');
        }
    }
}
