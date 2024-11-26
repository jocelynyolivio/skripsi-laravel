<?php

namespace App\Models;

// ini nanti untuk menyambungkan ke database
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

class Post
{
    static $blog_posts = [
        [
            "title" => "Judul A",
            "slug" => "judul-post-pertama",
            "author" => "Penulis 1",
            "body" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt laboriosam unde nihil ducimus quae. Modi vero recusandae a nam, excepturi enim, beatae soluta corrupti laborum sapiente eum, impedit quaerat hic?"
        ],
        [
            "title" => "Judul B",
            "slug" => "judul-post-kedua",
            "author" => "Penulis 2",
            "body" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt laboriosam unde nihil ducimus quae. Modi vero recusandae a nam, excepturi enim, beatae soluta corrupti laborum sapiente eum, impedit quaerat hic?"
        ]
        ];

    public static function all(){
        return collect(self::$blog_posts);
    }

    public static function find($slug){
        $posts = static::all();
        // $posts = self::$blog_posts;
        // $post = [];
        // foreach($posts as $p){
        //     if($p['slug'] === $slug){
        //         $post = $p;
        //     }
        // }
        
        // return $post;

        // akan selalu ambil data pertama, ini function dari collection
        // return $posts->first(); 

        // klo ini firstwhere condition
        return $posts->firstWhere('slug', $slug);

    }
}
