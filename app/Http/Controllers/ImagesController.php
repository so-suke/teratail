<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Log;

class ImagesController extends Controller {
  public function q_img($img_name) {

    Log::debug($img_name);

    $storagePath = storage_path("app/images/$img_name");

    return Image::make($storagePath)->response();
  }

  public function upload(Request $request) {
		$name = $request->image->getClientOriginalName();
		$path = $request->image->storeAs('images', $name);
		// $path = $request->image->store('images');
		$img_file_name = basename($path);
    return response()->json([
      'img_file_name' => $img_file_name,
    ]);
  }
}
