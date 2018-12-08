<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Session;

session_start();

class SliderController extends Controller
{
    public function index() {
            //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        return view('admin.add_slider');
    }
    
    public function save_slider(Request $request) {
        $data = array();
        $data['publication_status'] = $request->publication_status;
         $image = $request->file('slider_image');
        if ($image) {
            $image_name = str_random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name . '_' . $ext;
            $upload_path = 'slider/';
            $image_url = $upload_path . $image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $data['slider_image'] = $image_url;
                DB::table('tbl_slider')->insert($data);
                Session::put('message', 'Slider added succesfully!');
                return Redirect::to('/add-slider');
            }
        }
        $data['slider_image'] = '';
        DB::table('tbl_slider')->insert($data);
        Session::put('message', 'Sliders Added Successfully Without image!!');
        return Redirect('/add-slider');
    }
    
    public function all_slider() {
        //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        $all_slider = DB::table('tbl_slider')->get();
        $manage_slider = view('admin.all_slider')
                ->with('all_slider', $all_slider);
        return view('admin_layout')
                        ->with('admin.all_slider', $manage_slider);
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
