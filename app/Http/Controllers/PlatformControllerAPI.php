<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;

class PlatformControllerAPI extends Controller {

    public function updateEmail($email) {
        DB::table('config')
        ->where('id', 1)
        ->update(['platform_email' => $email]);
    }

}
