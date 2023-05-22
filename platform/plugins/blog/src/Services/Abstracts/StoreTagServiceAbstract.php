<?php

namespace Botble\Blog\Services\Abstracts;

use Botble\Blog\Models\Post;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Illuminate\Http\Request;

abstract class StoreTagServiceAbstract
{
    protected TagInterface $tagRepository;

    public function __construct(TagInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    abstract public function execute(Request $request, Post $post): void;
}
