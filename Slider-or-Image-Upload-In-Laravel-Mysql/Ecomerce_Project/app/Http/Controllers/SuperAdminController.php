<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Session;
use Illuminate\Support\Facades\Redirect;

session_start();

class SuperAdminController extends Controller {

    public function index() {
        //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        return view('admin.dashboard');
    }

    public function logout() {
        //Session::put('admin_name',null);
        //Session::put('admin_id',null);
        /* Use Session:: flush() insted of avobe 2 statement */
        Session::flush();
        return Redirect::to('/Admin');
    }
    //Cheking Admin/User validated or Secure
    public function AdminAuthCheck() {
        $AdminID = Session::get('admin_id');
        if ($AdminID) {
            return;   
        } else {
            return Redirect::to('/Admin')->send();
        }
    }

}
