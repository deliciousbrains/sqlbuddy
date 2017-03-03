<?php

namespace App\Http\Controllers\Api;

use DB;
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

        $schema = DB::getDoctrineSchemaManager();
        $result = $schema->listTableNames();

        return response()->json($result);
    }

    public function rows($database, $table)
    {
        $this->selectDatabase($database);

        $schema  = DB::getDoctrineSchemaManager();
        $columns = $schema->listTableColumns($table);
        $columns = collect($columns)->keys()->all();
        $rows    = DB::table($table)->paginate(20);

        $result = [
            'columns' => $columns,
            'rows'    => $rows,
        ];

        return response()->json($result);
    }
}
