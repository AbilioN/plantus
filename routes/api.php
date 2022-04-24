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

    Route::get('user/profile/find/{user_id?}' , 'UserProfileController@find')->name('user.profile.find');

    Route::get('user/health/find/{user_id?}', 'UserHealthController@find')->name('user.health.find');

    Route::get('user/professional/find/{user_id?}' , 'UserProfessionalController@find')->name('user.professional.find');

    Route::get('user/documents/find/{user_id?}' , 'UserDocumentController@find')->name('user.documents.update');
   
    Route::group(['middleware' => 'adminUser'] , function (){

        Route::post('user/documents/update/{user_id?}' , 'UserDocumentController@update')->name('user.documents.update');
        Route::post('user/health/update/{user_id?}', 'UserHealthController@update')->name('user.health.update');
        Route::post('user/profile/update/{user_id?}' , 'UserProfileController@update')->name('user.profile.update');
        Route::post('user/address/update/{user_id?}' , 'AddressController@update')->name('user.address.update');
        Route::post('user/professional/update/{user_id?}' , 'UserProfessionalController@update')->name('user.professional.update');
        Route::post('role/insert/{user_id?}','RoleController@insert')->name('role.insert');

    });

    Route::get('role/show' , 'RoleController@show')->name('role.show');

    
    Route::get('team/show' , 'TeamController@show')->name('team.show');
    Route::post('teste' , function(){

    });
});

// Route::post('user/documents/update' , 'UserDocumentController@update')->name('user.documents.update');

Route::post('/user/create' , 'UserController@create')->name('user.create');