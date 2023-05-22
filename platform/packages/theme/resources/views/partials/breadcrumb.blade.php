<nav aria-label="breadcrumb" class="d-inline-block">
    <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
            @if ($i != (count($crumbs) - 1))
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item">
                    <a href="{{ $crumb['url'] }}" itemprop="item" title="{{ $crumb['label'] }}">
                        {!! BaseHelper::clean($crumb['label']) !!}
                        <meta itemprop="name" content="{{ $crumb['label'] }}" />
                    </a>
                    <meta itemprop="position" content="{{ $i + 1}}" />
                </li>
            @else
                <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="item">
                        {!! BaseHelper::clean($crumb['label']) !!}
                    </span>
                    <meta itemprop="name" content="{{ $crumb['label'] }}" />
                    <meta itemprop="position" content="{{ $i + 1}}" />
                </li>
            @endif
        @endforeach
    </ol>
</nav>
