<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

// buat data req di url gtu
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){

        // dd(request('search'));

        $title = '';
        if(request('category')){
            $category = Category::firstWhere('slug', request('category'));
            $title = ' in ' . $category->name;
        }
        if(request('user')){
            $user = Category::firstWhere('username', request('user'));
            $title = ' by ' . $user->name;
        }
    
        return view('posts', [
            "title" => "All Post".$title,
            "posts" => Post::latest()->filter(request(['search', 'category', 'user']))->paginate(4)->withQueryString(),  // ini 4 tok sg muncul se halaman
            "active" => 'posts'
        ]);
    }
    

    // public function show($slug){
    //     return view('post', [
    //         "title" => "Single Post",
    //         "post" => Post::find($slug)
    //     ]);
    // }

    // route model binding biar gabisa edit2 di url
    public function show(Post $post){
        return view('post', [
            "title" => "Single Post",
            "post" => $post,
            "active" => 'post'
        ]);
    }
}
