@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">MY POSTS</h1>
</div>

@if(session()->has('success'))
<div class="alert alert-success">
  {{ session('success') }}
</div>
@endif


<div class="table-responsive small">
  <a href="/dashboard/posts/create" class="btn btn-primary mb-3">Create New Post</a>
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Juduk</th>
              <th scope="col">Category</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($posts as $post)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$post->title}}</td>
            <td>{{$post->category->name}}</td>
            <td>
                <a href="/dashboard/posts/{{$post->slug}}" class="badge bg-info">See</a>
                <a href="/dashboard/posts/{{$post->slug}}/edit" class="badge bg-warning">Edit</a>
                <form action="/dashboard/posts/{{$post->slug}}" method='post' class="d-inline">
                  @method('delete')
                  @csrf
                  <button class="badge bg-danger border-0" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
                <!-- <a href="/dashboard/posts/{{$post->id}}" class="badge bg-danger">Delete</a> -->
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
@endsection