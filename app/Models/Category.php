<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug'
    ];

    /**
     * posts
     *
     * @return void
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * sholawats
     *
     * @return void
     */
    public function sholawats()
    {
        return $this->hasMany(Sholawat::class);
    }

    /**
     * kerontangs
     *
     * @return void
     */
    public function kerontangs()
    {
        return $this->hasMany(Kerontang::class);
    }
}
