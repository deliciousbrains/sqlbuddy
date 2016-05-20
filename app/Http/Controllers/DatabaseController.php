<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $result = collect(app('db')->select('SHOW DATABASES'))->pluck('Database');
        }
        catch (Exception $e) {
            $result = false;
        }

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $database = $request->input('database');

        try {
            $result = app('db')->statement("CREATE DATABASE `{$database}`");
        }
        catch (Exception $e) {
            $result = false;
        }

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $result = app('db')->statement("DROP DATABASE `{$id}`");
        }
        catch (Exception $e) {
            $result = false;
        }

        return response()->json($result);
    }
}
