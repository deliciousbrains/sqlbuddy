<?php

namespace SQLBuddy\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Windwalker\Renderer\BladeRenderer;

class BaseController
{
    protected function view($template, $data = [])
    {
        $renderer = new BladeRenderer(resourcesPath() . '/views', [
            'cache_path' => storagePath() . '/cache',
        ]);

        return new Response($renderer->render($template, $data));
    }
}