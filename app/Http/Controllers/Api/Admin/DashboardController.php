<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use App\Models\Product;
use App\Models\Aparatur;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kerontang;
use App\Models\Sholawat;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //count categories
        $categories = Category::count();

        //count posts
        $posts = Post::count();

        //count products
        $products = Product::count();

        //count aparaturs
        $aparaturs = Aparatur::count();

        //count sholawats
        $sholawats = Sholawat::count();
        $kerontangs = Kerontang::count();

        //return response json
        return response()->json([
            'success'   => true,
            'message'   => 'List Data on Dashboard',
            'data'      => [
                'categories' => $categories,
                'posts'      => $posts,
                'products'   => $products,
                'aparaturs'  => $aparaturs,
                'sholawats'  => $sholawats,
                'kerontangs'  => $kerontangs,
            ]
        ]);
    }
}
