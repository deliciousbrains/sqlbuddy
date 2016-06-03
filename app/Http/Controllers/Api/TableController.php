<?php

namespace App\Http\Controllers\Api;

use DB;
use Exception;
use Illuminate\Http\Request;

class TableController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($database)
    {
        $this->selectDatabase($database);

        try {
            $schema = DB::getDoctrineSchemaManager();
            $result = $schema->listTableNames();
        } catch (Exception $e) {
            $result = $this->getError($e);
        }

        return response()->json($result);
    }

    public function rows($database, $table)
    {
        $this->selectDatabase($database);

        try {
            $schema  = DB::getDoctrineSchemaManager();
            $columns = $schema->listTableColumns($table);
            $columns = collect($columns)->keys()->all();
            $rows    = DB::table($table)->paginate(20);

            $result = [
                'columns' => $columns,
                'rows'    => $rows,
            ];
        } catch (Exception $e) {
            $result = $this->getError($e);
        }

        return response()->json($result);
    }
}
