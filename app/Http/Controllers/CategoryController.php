<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Category;

class CategoryController extends Controller
{
    public function index()
    {
    	$data = Category::paginate(10);
    	return response()->json($data);
    }
}
