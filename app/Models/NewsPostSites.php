<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class NewsPostSites extends Model
{
    // Explicitly define the hyphenated table name
    protected $table = 'news_post_sites';

    // Allow mass assignment for your column names
    protected $fillable = [
        'news_post_id',
        'wp_post_id',
        'post_title',
        'post_content',
        'post_image',
        'post_date',
        'post_status',
        'is_active',
        'site_name',
        'post_link',

    ];
}
