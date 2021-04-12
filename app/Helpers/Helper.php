<?php

namespace App\Helpers;


/**
 * This is a helper class to provide some facilty to the whole application 
 */

class Helper
{
    public static function uploadImage($file,$model,$business_id)
    {
        $img_name = null;
        if ($file) {
            $extname = $file->getClientOriginalExtension();
            $img_name = substr(md5(uniqid(rand(1, 6))) . microtime(true), 0, 15) . '.' . $extname;
            $file->move(public_path('images'), $img_name);
            $model->media()->create([
                'name' =>  $img_name,
                'type' => 'image',
                'business_id' => $business_id
            ]);
        }
        return $img_name;
    }
}
