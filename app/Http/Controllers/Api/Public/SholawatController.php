<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\KerontangResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\SholawatResource;
use App\Models\Sholawat;

class SholawatController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Sholawat::with('user', 'category')->latest()->paginate(10);

        //return with Api Resource
        return new SholawatResource(true, 'List Data Sholawat', $posts);
    }

    /**
     * index
     *
     * @return void
     */
    public function all()
    {
        $posts = Sholawat::with('user', 'category')->get();

        //return with Api Resource
        return new SholawatResource(true, 'List Data Sholawat', $posts);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $post = Sholawat::with('user', 'category')->where('slug', $slug)->first();

        if ($post) {
            //return with Api Resource
            return new SholawatResource(true, 'Detail Data Sholawat', $post);
        }

        //return with Api Resource
        return new SholawatResource(false, 'Detail Data Sholawat Tidak Ditemukan!', null);
    }

    /**
     * homePage
     *
     * @return void
     */
    public function homePage()
    {
        $posts = Sholawat::with('user', 'category')->latest()->take(6)->get();

        //return with Api Resource
        return new SholawatResource(true, 'List Data Post HomePage', $posts);
    }
}
