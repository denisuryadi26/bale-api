<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\KerontangResource;
use App\Http\Resources\PostResource;
use App\Models\Kerontang;

class KerontangController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Kerontang::with('user', 'category')->latest()->paginate(10);

        //return with Api Resource
        return new KerontangResource(true, 'List Data Posts', $posts);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $post = Kerontang::with('user', 'category')->where('slug', $slug)->first();

        if ($post) {
            //return with Api Resource
            return new KerontangResource(true, 'Detail Data Kerontang', $post);
        }

        //return with Api Resource
        return new KerontangResource(false, 'Detail Data Kerontang Tidak Ditemukan!', null);
    }

    /**
     * homePage
     *
     * @return void
     */
    public function homePage()
    {
        $posts = Kerontang::with('user', 'category')->latest()->take(6)->get();

        //return with Api Resource
        return new KerontangResource(true, 'List Data Post HomePage', $posts);
    }
}
