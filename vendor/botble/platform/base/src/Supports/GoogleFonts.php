<?php

namespace Botble\Base\Supports;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleFonts
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $path,
        protected bool $inline,
        protected string $userAgent,
    ) {
    }

    public function load(string $font, string|null $nonce = null, bool $forceDownload = false): Fonts
    {
        ['font' => $font, 'nonce' => $nonce] = $this->parseOptions($font, $nonce);

        $url = $font;

        try {
            if ($forceDownload) {
                return $this->fetch($url, $nonce);
            }

            $fonts = $this->loadLocal($url, $nonce);

            if (! $fonts) {
                return $this->fetch($url, $nonce);
            }

            return $fonts;
        } catch (Exception $exception) {
            if (config('app.debug')) {
                throw $exception;
            }

            return new Fonts(googleFontsUrl: $url, nonce: $nonce);
        }
    }

    protected function loadLocal(string $url, ?string $nonce): ?Fonts
    {
        if (! $this->filesystem->exists($this->path($url, 'fonts.css'))) {
            return null;
        }

        $localizedCss = $this->filesystem->get($this->path($url, 'fonts.css'));

        if (! str_contains($localizedCss, Storage::url('fonts'))) {
            $localizedCss = preg_replace('/(http|https):\/\/.*?\/storage\/fonts\//i', Storage::url('fonts/'), $localizedCss);
            $this->filesystem->put($this->path($url, 'fonts.css'), $localizedCss);
        }

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->filesystem->url($this->path($url, 'fonts.css')),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function fetch(string $url, ?string $nonce): Fonts
    {
        $css = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->timeout(300)
            ->get($url)
            ->body();

        $localizedCss = $css;

        foreach ($this->extractFontUrls($css) as $fontUrl) {
            $localizedFontUrl = $this->localizeFontUrl($fontUrl);

            $this->filesystem->put(
                $this->path($url, $localizedFontUrl),
                Http::get($fontUrl)->body(),
            );

            $localizedCss = str_replace(
                $fontUrl,
                $this->filesystem->url($this->path($url, $localizedFontUrl)),
                $localizedCss,
            );
        }

        $this->filesystem->put($this->path($url, 'fonts.css'), $localizedCss);

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->filesystem->url($this->path($url, 'fonts.css')),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function extractFontUrls(string $css): array
    {
        $matches = [];
        preg_match_all('/url\((https:\/\/fonts.gstatic.com\/[^)]+)\)/', $css, $matches);

        return $matches[1] ?? [];
    }

    protected function localizeFontUrl(string $path): string
    {
        [$path, $extension] = explode('.', str_replace('https://fonts.gstatic.com/', '', $path));

        return implode('.', [Str::slug($path), $extension]);
    }

    protected function path(string $url, string $path = ''): string
    {
        $segments = collect([
            $this->path,
            substr(md5($url), 0, 10),
            $path,
        ]);

        return $segments->filter()->join('/');
    }

    protected function parseOptions(string $font, string|null $nonce = null): array
    {
        return [
            'font' => $font,
            'nonce' => $nonce,
        ];
    }
}
