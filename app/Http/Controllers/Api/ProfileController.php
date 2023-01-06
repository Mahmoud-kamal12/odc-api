<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request){
        $data = $request->only('name', 'email', 'phone', 'education', 'level_id');

        $user = auth()->user();

        if ($user->update($data)){
            return response()->json(['msg' => 'user updated successfully']);
        }else{
            return response()->json(['msg' => 'error on update']);
        }
    }
}
