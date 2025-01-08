<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'carousel_image',
        'carousel_text',
        'welcome_title',
        'welcome_message',
        'about_text',
        'about_image',
        'services_text',
    ];
    
}
