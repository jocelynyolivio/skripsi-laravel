@extends('layouts.main')

@section('container')

<div class="row justify-content-center text-center">
    <div class="col-md-6">
        <main class="form-registration w-100 m-auto">
            <h1 class="h3 mb-3 fw-normal">Registration Form</h1>
            <form action="/register" method="post">
                @csrf
                <div class="form-floating">
                    <input type="text" class="form-control rounded-top @error('name') is-invalid @enderror" id="name" name="name" placeholder="name" required value="{{old('nama')}}">
                    <label for="name">Name</label>
                    @error('name')
                    <div class="invalid-feedback">
                        Please input name.
                    </div>
                    @enderror
                </div>
                <div class="form-floating">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" required value="{{old('email')}}">
                    <label for="email">Email address</label>
                    @error('email')
                    <div class="invalid-feedback">
                        Please input a valid email.
                    </div>
                    @enderror
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control rounded-bottom @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    @error('password')
                    <div class="invalid-feedback">
                        Password must be atleast 5 character.
                    </div>
                    @enderror
                </div>
                <button class="btn btn-primary w-100 py-2 mt-3" type="submit">Register</button>
            </form>
            <small class="d-block mt-3">Already have an account? <a href="/login">Login here.</a></small>
        </main>
    </div>
</div>
@endsection