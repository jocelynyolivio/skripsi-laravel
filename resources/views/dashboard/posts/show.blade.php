@extends('dashboard.layouts.main')

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
        @if ($post->image)
        <div style="max-height: 350px; overflow:hidden">
        <img src="{{asset('storage/'. $post->image)}}" class="img-fluid mt-3" alt="...">
        </div>
        @else
        <img src="https://images.unsplash.com/photo-1508557344244-0c3c025b92a3?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=f94c5fc58d29bb4fafd2312da8c02f84&auto=format&fit=crop&w=1056&q=80" class="img-fluid mt-3" alt="...">
        @endif
    </div>
</div>



<article>
    
</article>
@endsection