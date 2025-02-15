@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
<h1>Add New Content</h1>

<form action="{{ route('dashboard.home_content.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="carousel_image" class="form-label">Carousel Image</label>
        <input type="file" class="form-control" id="carousel_image" name="carousel_image">
    </div>

    <div class="mb-3">
        <label for="carousel_text" class="form-label">Carousel Text</label>
        <input type="text" class="form-control" id="carousel_text" name="carousel_text" value="{{ old('carousel_text') }}">
    </div>

    <div class="mb-3">
        <label for="welcome_title" class="form-label">Welcome Title</label>
        <input type="text" class="form-control" id="welcome_title" name="welcome_title" value="{{ old('welcome_title') }}">
    </div>

    <div class="mb-3">
        <label for="welcome_message" class="form-label">Welcome Message</label>
        <textarea class="form-control" id="welcome_message" name="welcome_message">{{ old('welcome_message') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="about_text" class="form-label">About Text</label>
        <textarea class="form-control" id="about_text" name="about_text">{{ old('about_text') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="about_image" class="form-label">About Image</label>
        <input type="file" class="form-control" id="about_image" name="about_image">
    </div>

    <div class="mb-3">
        <label for="services_text" class="form-label">Services Text</label>
        <textarea class="form-control" id="services_text" name="services_text">{{ old('services_text') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save Content</button>
</form>
</div>
@endsection
