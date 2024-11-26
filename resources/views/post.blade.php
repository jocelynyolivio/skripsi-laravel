@extends('layouts.main')

@section('container')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $post->title }}</h2>
            <!-- <h5>{{ $post->author }}</h5> -->
            <!-- ini kalau gaada html didaleme -->
            <!-- {{ $post->body }} -->
            <p>By {{$post->user->name}} in <a href="/blog?category={{$post->category->slug}} ">{{$post->category->name}}</a></p>
            <!-- kl ada tag html misal <p> -->
                {!! $post->body !!}
            <a href="/blog">Back to blog posts</a>
        </div>
    </div>
</div>



<article>
    
</article>
@endsection