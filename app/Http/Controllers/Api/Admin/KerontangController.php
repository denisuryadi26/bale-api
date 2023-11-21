<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\KerontangResource;
use App\Models\Kerontang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KerontangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->id == 1) {
            $kerontangs = Kerontang::with('user', 'category')->when(request()->search, function ($kerontangs) {
                $kerontangs = $kerontangs->where('title', 'like', '%' . request()->search . '%');
            })->latest()->paginate(5);
        } else {
            $kerontangs = Kerontang::with('user', 'category')->when(request()->search, function ($kerontangs) {
                $kerontangs = $kerontangs->where('title', 'like', '%' . request()->search . '%');
            })->where('user_id', auth()->user()->id)->latest()->paginate(5);
        }
        // $kerontangs = Kerontang::with('user', 'category')->when(request()->search, function ($kerontangs) {
        //     $kerontangs = $kerontangs->where('title', 'like', '%' . request()->search . '%');
        // })->where('user_id', auth()->user()->id)->latest()->paginate(5);

        //append query string to pagination links
        $kerontangs->appends(['search' => request()->search]);

        //return with Api Resource
        return new KerontangResource(true, 'List Data Kerontangs', $kerontangs);
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
            // 'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:kerontangs',
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/kerontangs', $image->hashName());

        $kerontang = Kerontang::create([
            // 'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);


        if ($kerontang) {
            //return success with Api Resource
            return new KerontangResource(true, 'Data Kerontang Berhasil Disimpan!', $kerontang);
        }

        //return failed with Api Resource
        return new KerontangResource(false, 'Data Kerontang Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kerontang = Kerontang::with('category')->whereId($id)->first();

        if ($kerontang) {
            //return success with Api Resource
            return new KerontangResource(true, 'Detail Data Kerontang!', $kerontang);
        }

        //return failed with Api Resource
        return new KerontangResource(false, 'Detail Data Kerontang Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kerontang $kerontang)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:kerontangs,title,' . $kerontang->id,
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        // if ($request->file('image')) {

        //     //remove old image
        //     Storage::disk('local')->delete('public/kerontangs/' . basename($post->image));

        //     //upload new image
        //     $image = $request->file('image');
        //     $image->storeAs('public/kerontangs', $image->hashName());

        //     $post->update([
        //         'image'       => $image->hashName(),
        //         'title'       => $request->title,
        //         'slug'        => Str::slug($request->title, '-'),
        //         'category_id' => $request->category_id,
        //         'user_id'     => auth()->guard('api')->user()->id,
        //         'content'     => $request->content
        //     ]);
        // }

        $kerontang->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);

        if ($kerontang) {
            //return success with Api Resource
            return new KerontangResource(true, 'Data Kerontang Berhasil Diupdate!', $kerontang);
        }

        //return failed with Api Resource
        return new KerontangResource(false, 'Data Kerontang Gagal Disupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kerontang $kerontang)
    {
        //remove image
        // Storage::disk('local')->delete('public/kerontangs/' . basename($post->image));

        if ($kerontang->delete()) {
            //return success with Api Resource
            return new KerontangResource(true, 'Data Kerontang Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new KerontangResource(false, 'Data Kerontang Gagal Dihapus!', null);
    }
}
