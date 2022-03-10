<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download' , function (){

    $url = "address/1/ftI0c6gxYOsjGR1hw2tMnhoCvOSfnSsFzR2tm26c.pdf";


    $headers = array(
        'Content-Type' => 'application/pdf',
    );
   $file =  Storage::download($url);
    // dd($file);
   $url =  Storage::url($url);


// dd($file);

    // return view('teste' , compact('url'));
//    return response($url)
//             ->withHeaders([
//                 'Content-Type' => 'application/jpg',
//                 'X-Header-One' => 'Header Value',
//                 'X-Header-Two' => 'Header Value',
//             ]);

return response()->download($url);

});
