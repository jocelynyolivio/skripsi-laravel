@extends('layouts.main')

@section('container')
<h1 class="mb-3 text-center">Welcome to {{$title}}</h1>

<div class="row justify-content-center mb-3">
    <div class="col-md-6">
        <form action="/blog">
            @if(request('category'))
            <input type="hidden" name="category" value="{{request('category')}}">
            @endif
            @if(request('user'))
            <input type="hidden" name="user" value="{{request('user')}}">
            @endif
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search.." name="search" value="{{request('search')}}">
                <button class="btn btn-danger" type="submit">Search</button>
            </div>

        </form>
    </div>
</div>

@if($posts->count())
<div class="card mb-3 text-center">
    @if ($posts[0]->image)
    <div style="max-height: 350px; overflow:hidden">
        <img src="{{asset('storage/'. $posts[0]->image)}}" class="img-fluid mt-3" alt="...">
    </div>
    @else
    <img src="https://images.unsplash.com/photo-1508557344244-0c3c025b92a3?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=f94c5fc58d29bb4fafd2312da8c02f84&auto=format&fit=crop&w=1056&q=80" class="card-img-top" alt="...">
    @endif

    <div class="card-body">
        <h5 class="card-title">{{$posts[0]->title}}</h5>
        <p>
            <small class="text-body-secondary">
                By <a href="/blog?user={{$posts[0]->user->username}} ">{{ $posts[0]->user->name }}</a> in <a href="/blog?category={{$posts[0]->category->slug}} " class="text-decoration-none">{{$posts[0]->category->name}}</a> {{ $posts[0]->created_at->diffForHumans() }}
            </small>
        </p>
        <p class="card-text">{{ $posts[0]->excerpt}}</p>

        <a href="/post/{{$posts[0]->slug}}" class="text-decoration-none btn btn-primary">Read More</a>
    </div>
</div>
<div class="container">
    <div class="row">
        @foreach ($posts->skip(1) as $post)
        <div class="col-md-4 mb-3">
            <div class="card" style="width: 18rem;">
                <div class="position-absolute bg-dark px-3 py-2 text-white">{{$post->category->name}}</div>

                @if ($post->image)
                <div style="max-height: 350px; overflow:hidden">
                    <img src="{{asset('storage/'. $post->image)}}" class="img-fluid mt-3" alt="...">
                </div>
                @else
                <img src="https://images.unsplash.com/photo-1508557344244-0c3c025b92a3?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=f94c5fc58d29bb4fafd2312da8c02f84&auto=format&fit=crop&w=1056&q=80" class="card-img-top" alt="...">
                @endif



                <div class="card-body">
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p>By <a href="/blog?user={{$post->user->username}} ">{{ $post->user->name }}</a> in <a href="/blog?category={{$post->category->slug}} " class="text-decoration-none">{{$post->category->name}} </a>{{ $posts[0]->created_at->diffForHumans() }}</p>
                    <p>{{$post->excerpt}}</p>
                    <a href="/post/{{$post->slug}}" class="text-decoration-none">Read More</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<p class="text-center fs-4">No post found.</p>
@endif

{{ $posts->links() }}

@endsection