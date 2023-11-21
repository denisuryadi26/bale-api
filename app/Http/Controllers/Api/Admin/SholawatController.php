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
        if (auth()->user()->id == 1) {
            $sholawats = Sholawat::with('user', 'category')->when(request()->search, function ($sholawats) {
                $sholawats = $sholawats->where('title', 'like', '%' . request()->search . '%');
            })->latest()->paginate(5);
        } else {
            $sholawats = Sholawat::with('user', 'category')->when(request()->search, function ($sholawats) {
                $sholawats = $sholawats->where('title', 'like', '%' . request()->search . '%');
            })->where('user_id', auth()->user()->id)->latest()->paginate(5);
        }
        // $sholawats = Sholawat::with('user', 'category')->when(request()->search, function ($sholawats) {
        //     $sholawats = $sholawats->where('title', 'like', '%' . request()->search . '%');
        // })->where('user_id', auth()->user()->id)->latest()->paginate(5);

        //append query string to pagination links
        $sholawats->appends(['search' => request()->search]);

        //return with Api Resource
        return new SholawatResource(true, 'List Data Sholawats', $sholawats);
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
            'title'         => 'required|unique:sholawats',
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/sholawats', $image->hashName());

        $sholawat = Sholawat::create([
            // 'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);


        if ($sholawat) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Disimpan!', $sholawat);
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
        $sholawat = Sholawat::with('category')->whereId($id)->first();

        if ($sholawat) {
            //return success with Api Resource
            return new SholawatResource(true, 'Detail Data Sholawat!', $sholawat);
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
    public function update(Request $request, Sholawat $sholawat)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:sholawats,title,' . $sholawat->id,
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        // if ($request->file('image')) {

        //     //remove old image
        //     Storage::disk('local')->delete('public/sholawats/' . basename($sholawat->image));

        //     //upload new image
        //     $image = $request->file('image');
        //     $image->storeAs('public/sholawats', $image->hashName());

        //     $sholawat->update([
        //         'image'       => $image->hashName(),
        //         'title'       => $request->title,
        //         'slug'        => Str::slug($request->title, '-'),
        //         'category_id' => $request->category_id,
        //         'user_id'     => auth()->guard('api')->user()->id,
        //         'content'     => $request->content
        //     ]);
        // }

        $sholawat->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);

        if ($sholawat) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Diupdate!', $sholawat);
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
    public function destroy(Sholawat $sholawat)
    {
        //remove image
        // Storage::disk('local')->delete('public/sholawats/' . basename($sholawat->image));

        if ($sholawat->delete()) {
            //return success with Api Resource
            return new SholawatResource(true, 'Data Sholawat Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new SholawatResource(false, 'Data Sholawat Gagal Dihapus!', null);
    }
}
