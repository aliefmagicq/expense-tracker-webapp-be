<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function create (Request $request) 
    {
        $validate = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|unique:branches,name',
            'description' => 'nullable|string'
        ]);

        if ($validate->fails()) 
            return ResponseController::fails(
                'validation error' , $validate->errors()->getMessageBag(), null, 422
            );

        $data = $validate->validated();
        $newBranch = Branch::query()->create([...$data, 'author_id' => $request->user()->id]);

        return ResponseController::success(
            'create branch successful', $newBranch, 201
        );
    }
    
    public function getBranches (Request $request)
    {
        // $user = $request->user();
        // if (!$user) {
        //     return ResponseController::fails(
        //         'unauthorized', '', null, 401
        //     );
        // }
        
        $branches = Branch::query()->get();
        return ResponseController::success(
            'get all branches', $branches, 200
        );
    }
}
