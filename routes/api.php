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

Route::group(['prefix' => 'auth'] , function (){
    Route::post('login', 'AuthController@authenticate');
    Route::post('register', 'AuthController@register');
    Route::post('forgot-password', 'AuthController@forget')->name('password.email');
    Route::post('reset-password', 'AuthController@reset')->name('password.reset');
});


Route::group(['middleware' => ['jwt.verify']], function() {

//    user Routes
    Route::post('auth/update-password', 'AuthController@updatePassword')->name('password.update');
    Route::get('auth/logout', 'AuthController@logout')->name('logout');
    Route::post('profile/edit' , 'ProfileController@edit');

//    Blog Routes
    Route::apiResource('posts' , 'PostController');

//    Levels Routes
    Route::apiResource('levels' , 'LevelController');

//    Orders Routes
    Route::apiResource('orders' , 'OrderController');
    Route::get('old-orders' , 'OrderController@oldOrders');

//    Plants Routes
    Route::apiResource('plants' , 'PlantController');

//    Shopping Cart Routes
    Route::apiResource('shopping-cart' , 'ShoppingCartController');

//    Shopping Cart Routes
    Route::apiResource('questions' , 'QuestionController');

//    Quiz Routes
    Route::apiResource('quizzes' , 'QuizController');
    Route::post('quizzes/correct/{id}' , 'QuizController@correct');


});
