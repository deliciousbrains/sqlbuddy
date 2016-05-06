<?php

namespace SQLBuddy;

use SQLBuddy\Interfaces\Renderer;
use Windwalker\Renderer\BladeRenderer as WindwalkerBladeRenderer;

class BladeRenderer implements Renderer
{
    protected $renderer;

    public function __construct()
    {
        $this->renderer = new WindwalkerBladeRenderer(resourcesPath() . '/views', [
            'cache_path' => storagePath() . '/cache',
        ]);
    }

    public function render($template, $data = [])
    {
        return $this->renderer->render($template, $data);
    }
}