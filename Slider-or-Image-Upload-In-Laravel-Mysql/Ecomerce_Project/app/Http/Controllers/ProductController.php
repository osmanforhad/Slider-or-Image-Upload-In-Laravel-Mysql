<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Session;

session_start();

class ProductController extends Controller {

    //index means add product page
    public function index() {
        //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        return view('admin.add_product');
    }

    public function save_product(Request $request) {
        $data = array();
        $data['product_name'] = $request->product_name;
        $data['category_id'] = $request->category_id;
        $data['manufacture_id'] = $request->manufacture_id;
        $data['product_short_description'] = $request->product_short_description;
        $data['product_long_description'] = $request->product_long_description;
        $data['product_price'] = $request->product_price;
        $data['product_size'] = $request->product_size;
        $data['product_color'] = $request->product_color;
        $data['publication_status'] = $request->publication_status;

        $image = $request->file('product_image');
        if ($image) {
            $image_name = str_random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name . '_' . $ext;
            $upload_path = 'image/';
            $image_url = $upload_path . $image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $data['product_image'] = $image_url;
                DB::table('tbl_products')->insert($data);
                Session::put('message', 'Product added succesfully!');
                return Redirect::to('/add-product');
            }
        }
        $data['product_image'] = '';
        DB::table('tbl_products')->insert($data);
        Session::put('message', 'Product Added Successfully Without image!!');
        return Redirect('/add-product');
    }

    public function all_product() {
        //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        $all_product_info = DB::table('tbl_products')
                //Join Table tbl_category with tbl_product and join category_id (column) with tbl_category and category_id (column) (Relational DB)
                ->join('tbl_category', 'tbl_products.category_id', '=', 'tbl_category.category_id')
                //Join Table tbl_manufacture with tbl_product and join manufacture_id (column) with tbl_manufacture and manufacture_id (column) (Relational DB)
                ->join('tbl_manufacture', 'tbl_products.manufacture_id', '=', 'tbl_manufacture.manufacture_id')
                //Advanced Join Techniq for Display Different Column from Your jointed table
//Extra Note:You will understand if you follow form Upon code and you can follow Database and follow all_product.blade.php page
                ->select('tbl_products.*', 'tbl_category.category_name', 'tbl_manufacture.manufacture_name')
                ->get();
        $manage_product = view('admin.all_product')
                ->with('all_product_info', $all_product_info);
        return view('admin_layout')
                        ->with('admin.all_product', $manage_product);
    }

    public function unactive_product($product_id) {
        DB::table('tbl_products')
                ->where('product_id', $product_id)
                ->update(['publication_status' => 0]);
        Session::put('message', 'Product Unactive Successfully !!');
        return Redirect::to('/all-product');
    }

    public function active_product($product_id) {
        DB::table('tbl_products')
                ->where('product_id', $product_id)
                ->update(['publication_status' => 1]);
        Session::put('message', 'Product Active Successfully !!');
        return Redirect::to('/all-product');
    }

    public function delete_product($product_id) {
        DB::table('tbl_products')
                ->where('product_id', $product_id)
                ->delete();
        Session::put('message', 'Product Deleted Successfully!');
        return Redirect::to('/all-product');
    }

    public function edit_product($product_id) {
        $product_info = DB::table('tbl_products')
                ->where('product_id', $product_id)
                ->first();

        $product_info = view('admin.edit_product')
                ->with('product_info', $product_info);
        return view('admin_layout')
                        ->with('admin.edit_product', $product_info);
        //return view('admin.edit_category');
    }

    public function update_product(Request $request, $product_id) {
        //Calling here The function AdminAuthCheck() for Security
        $this->AdminAuthCheck();
        $data = array();
        $data['product_name'] = $request->product_name;
        $data['product_short_description'] = $request->product_short_description;
        $data['product_price'] = $request->product_price;
        DB::table('tbl_products')
                ->where('product_id', $product_id)
                ->update($data);
        Session::put('message', 'Product update successfully !');
        return Redirect::to('/all-product');
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
