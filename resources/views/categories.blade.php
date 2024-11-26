@extends('layouts.main')

@section('container')
<h1 class="mb-3 text-center">Post Categories</h1>

<div class="container">
    <div class="row">
        @foreach ($category as $category)
        <div class="col-md-4 mb-3">
            <a href="/blog?category={{$category->slug}}">
                <div class="card text-bg-dark text-white">
                    <img src="https://images.unsplash.com/photo-1508557344244-0c3c025b92a3?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=f94c5fc58d29bb4fafd2312da8c02f84&auto=format&fit=crop&w=1056&q=80" class="card-img" alt="...">
                    <div class="card-img-overlay d-flex align-items-center p-0">
                        <h5 class="card-title text-center flex-fill p-4 bg-dark bg-opacity-50">{{ $category->name }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
