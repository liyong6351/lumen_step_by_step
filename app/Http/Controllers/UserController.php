<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function show($id){
        return response()->json(['data'=>'hello world']);
    }
}