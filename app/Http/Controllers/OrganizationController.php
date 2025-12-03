<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function create (Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|unique:organizations,name',
            'description' => 'nullable|string',
        ]);

        if ($validate->fails()) 
            return ResponseController::fails(
                'validation err', $validate->errors()->getMessageBag(), null, 422
            );

        $data = $validate->validated();
        $newOrg = $request->user()->organizations()->create($data);

        return response()->json([
            'new_organization' => $newOrg
        ]);
    }
}
