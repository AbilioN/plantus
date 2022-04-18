<?php 

namespace App\Http\Responses;


class Response 
{


    public function success($data)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], 
        200,
        ['Content-type' => 'application/json;charset=UTF-8' , 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE
        );
    }

    public static function badRequest($data)
    {
        return response()->json([
            'success' => false,
            'errors' => $data
        ], 
        400,
        ['Content-type' => 'application/json;charset=UTF-8' , 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE
        );
    }

    public static function notFound($data)
    {
        return response()->json([
            'success' => false,
            'errors' => $data
        ], 
        404,
        ['Content-type' => 'application/json;charset=UTF-8' , 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE
        );
    }
}