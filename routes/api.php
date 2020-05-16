<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@register');
Route::middleware('auth:sanctum')->put('/profile', 'UserController@profile');
Route::middleware('auth:sanctum')->post('/content/add', 'ContentController@store');
Route::middleware('auth:sanctum')->get('/content/list', 'ContentController@index');
Route::middleware('auth:sanctum')->put('/content/like/{content}', 'ContentController@like');
Route::middleware('auth:sanctum')->put('/content/like/page/{content}', 'ContentController@likePage');
Route::middleware('auth:sanctum')->put('/content/comments/{content}', 'ContentController@comments');
Route::middleware('auth:sanctum')->put('/content/comments/page/{content}', 'ContentController@commentsPage');
Route::middleware('auth:sanctum')->get('/content/page/{user}', 'ContentController@page');
Route::middleware('auth:sanctum')->post('/user/friend', 'UserController@friend');
Route::middleware('auth:sanctum')->get('/user/list/friend', 'UserController@ListFriend');
Route::middleware('auth:sanctum')->get('/user/list/page/friend/{user}', 'UserController@ListPageFriend');


Route::get('/teste', function (){
   $teste = \App\Content::all();
   foreach ($teste as $t){
       $t->delete();
   }

   return 'ok';
});
