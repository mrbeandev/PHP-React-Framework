<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seo';
    protected $fillable = ['path', 'title', 'description', 'keywords', 'og_image'];
}
