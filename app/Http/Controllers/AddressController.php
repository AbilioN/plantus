<?php

namespace App\Http\Controllers;

use App\Repositories\UserAddressRepository;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private UserAddressRepository $repo;
    public function __construct(UserAddressRepository $repo)
    {
        $this->repo = $repo;
    }

    public function update(Request $request)
    {

        $data = $request->all();
        $response = $this->repo->updateUserAddress($data);
        return response()->json($response, 200);
        
    }
}
