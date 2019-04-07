<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//list pdf files
Route::middleware('auth:api')->get('pdf_files','PDFFileController@index');
//list single pdf file //should open this file
Route::middleware('auth:api')->get('pdf_files/{id}','PDFFileController@show');
//create (upload) new pdf file
Route::middleware('auth:api')->post('pdf_file','PDFFileController@store');
//open in browser
//view pdf in browser
Route::middleware('auth:api')->get('pdf_files/view/{id}','PDFFileController@view');
//no update
//delete pdf files
Route::middleware('auth:api')->post('pdf_files/delete/{id}','PDFFileController@destroy');
