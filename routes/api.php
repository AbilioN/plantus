<?php

use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});




Route::group(['middleware' => 'basicUser'], function(){

    Route::post('/user/address/update' , 'AddressController@update')->name('user.address.update');
    Route::post('user/profile/update' , 'UserProfileController@update')->name('user.profile.update');
    Route::get('user/profile/find' , 'UserProfileController@find')->name('user.profile.find');

    Route::post('user/health/update', 'UserHealthController@update')->name('user.health.update');
    Route::get('user/health/find', 'UserHealthController@find')->name('user.health.find');


    Route::post('user/professional/update' , 'UserProfessionalController@update')->name('user.professional.update');
    Route::get('user/professional/find' , 'UserProfessionalController@find')->name('user.professional.find');


    Route::post('teste' , function(){

    });
});

Route::post('user/documents/update' , 'UserDocumentController@update')->name('user.documents.update');

Route::post('/user/create' , 'UserController@create')->name('user.create');