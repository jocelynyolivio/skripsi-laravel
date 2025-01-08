@extends('dashboard.layouts.main')

@section('container')
<h1>Edit Content</h1>

<form action="{{ route('dashboard.home_content.update', $content->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="carousel_image" class="form-label">Carousel Image</label>
        <input type="file" class="form-control" id="carousel_image" name="carousel_image">
        @if($content->carousel_image)
            <img src="{{ asset('storage/' . $content->carousel_image) }}" alt="Carousel Image" width="100" class="mt-2">
        @endif
    </div>

    <div class="mb-3">
        <label for="carousel_text" class="form-label">Carousel Text</label>
        <input type="text" class="form-control" id="carousel_text" name="carousel_text" value="{{ old('carousel_text', $content->carousel_text) }}">
    </div>

    <div class="mb-3">
        <label for="welcome_title" class="form-label">Welcome Title</label>
        <input type="text" class="form-control" id="welcome_title" name="welcome_title" value="{{ old('welcome_title', $content->welcome_title) }}">
    </div>

    <div class="mb-3">
        <label for="welcome_message" class="form-label">Welcome Message</label>
        <textarea class="form-control" id="welcome_message" name="welcome_message">{{ old('welcome_message', $content->welcome_message) }}</textarea>
    </div>

    <div class="mb-3">
        <label for="about_text" class="form-label">About Text</label>
        <textarea class="form-control" id="about_text" name="about_text">{{ old('about_text', $content->about_text) }}</textarea>
    </div>

    <div class="mb-3">
        <label for="about_image" class="form-label">About Image</label>
        <input type="file" class="form-control" id="about_image" name="about_image">
        @if($content->about_image)
            <img src="{{ asset('storage/' . $content->about_image) }}" alt="About Image" width="100" class="mt-2">
        @endif
    </div>

    <div class="mb-3">
        <label for="services_text" class="form-label">Services Text</label>
        <textarea class="form-control" id="services_text" name="services_text">{{ old('services_text', $content->services_text) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Content</button>
</form>
@endsection
