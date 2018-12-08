<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;

class HomeController extends Controller
{
    //This function Display Landing Page or Home page
    public function index() {
        $all_published_product = DB::table('tbl_products')
                //Join Table tbl_category with tbl_product and join category_id (column) with tbl_category and category_id (column) (Relational DB)
                ->join('tbl_category', 'tbl_products.category_id', '=', 'tbl_category.category_id')
                //Join Table tbl_manufacture with tbl_product and join manufacture_id (column) with tbl_manufacture and manufacture_id (column) (Relational DB)
                ->join('tbl_manufacture', 'tbl_products.manufacture_id', '=', 'tbl_manufacture.manufacture_id')
                //Advanced Join Techniq for Display Different Column from Your jointed table
//Extra Note:You will understand if you follow form Upon code and you can follow Database and follow all_product.blade.php page
                ->select('tbl_products.*', 'tbl_category.category_name', 'tbl_manufacture.manufacture_name')
                //Specify which tables publication_status are used altgho we have 3 tablewhose column name publication_status are same
                ->where('tbl_products.publication_status',1)
                ->limit(6)
                ->get();
        $manage_published_product = view('pages.home_content')
                ->with('all_published_product', $all_published_product);
        return view('layout')
                        ->with('pages.home_content', $manage_published_product);
        //return view('pages.home_content');
    }
}
