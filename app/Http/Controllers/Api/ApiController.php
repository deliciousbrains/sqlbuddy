<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * @param string $databaseName
     */
    protected function selectDatabase($databaseName)
    {
        $pdo = \DB::connection()->getPdo();
        $pdo->exec('use `' . $databaseName . '`');
    }
}
