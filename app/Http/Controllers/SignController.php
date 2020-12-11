<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\signe;
use Image;
use Illuminate\Support\Facades\Response;
use Storage;
use File;
use Illuminate\Support\Facades\Auth;

use Validator;
use App\Models\User;

class SignController extends Controller
{
    public function index()
    {
        $books = signe::latest()->paginate(2);
       // return array_reverse($books);
       return response()->json($books);
    }

    public function signe()
    {
        $books = signe::all()->toArray();
        return array_reverse($books);
    }

    function insert_image(Request $request)
    {

        $image_file = $request->user_image;
        
        $form_data = array(
           'user_image' => $image_file
        );

       

        signe::create($form_data);

        return response()
        ->json(['status' => 'successs'], 200);
    
    }

    function fetch_image($image_id)
    {
     $image = signe::findOrFail($image_id);

     $image_file = Image::make($image->user_image);

     $response = Response::make($image_file->encode('jpeg'));

     $response->header('Content-Type', 'image/jpeg');

     return $response;
    }

    public function destroy($id)
    {
        //

        $user = signe::findOrFail($id);
        $filePath = $user->image;

        if (file_exists($filePath)) {

        unlink($filePath);
        }

        $user -> delete();
        return back()->with('success', 'success delete');
    }
}
