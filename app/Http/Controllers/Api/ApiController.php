<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * @param \Exception $e
     * @return array
     */
    protected function getError(\Exception $e)
    {
        $result = [
            'error' => $e->getMessage(),
        ];

        if (env('APP_DEBUG')) {
            $result['stack_trace'] = explode("\n", $e->getTraceAsString());
        }

        return $result;
    }

    /**
     * @param string $databaseName
     */
    protected function selectDatabase($databaseName)
    {
        $pdo = \DB::connection()->getPdo();
        $pdo->exec('use `' . $databaseName . '`');
    }
}
