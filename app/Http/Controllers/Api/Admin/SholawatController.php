<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\KerontangResource;
use App\Http\Resources\SholawatResource;
use App\Models\Kerontang;
use App\Models\Sholawat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SholawatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Sholawat::with('user', 'category')->when(request()->search, function ($posts) {
            $posts = $posts->where('title', 'like', '%' . request()->search . '%');
        })->where('user_id', auth()->user()->id)->latest()->paginate(5);

        //append query string to pagination links
        $posts->appends(['search' => request()->search]);

        //return with Api Resource
        return new SholawatResource(true, 'List Data Posts', $posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:sholawats',
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/sholawats', $image->hashName());

        $post = Sholawat::create([
            'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);


        if ($post) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Disimpan!', $post);
        }

        //return failed with Api Resource
        return new SholawatResource(false, 'Data Sholawat Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Sholawat::with('category')->whereId($id)->first();

        if ($post) {
            //return success with Api Resource
            return new SholawatResource(true, 'Detail Data Sholawat!', $post);
        }

        //return failed with Api Resource
        return new SholawatResource(false, 'Detail Data Sholawat Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:sholawats,title,' . $post->id,
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/sholawats/' . basename($post->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/sholawats', $image->hashName());

            $post->update([
                'image'       => $image->hashName(),
                'title'       => $request->title,
                'slug'        => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id'     => auth()->guard('api')->user()->id,
                'content'     => $request->content
            ]);
        }

        $post->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);

        if ($post) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Diupdate!', $post);
        }

        //return failed with Api Resource
        return new SholawatResource(false, 'Data Sholawat Gagal Disupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //remove image
        Storage::disk('local')->delete('public/sholawats/' . basename($post->image));

        if ($post->delete()) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new SholawatResource(false, 'Data Sholawat Gagal Dihapus!', null);
    }
}
