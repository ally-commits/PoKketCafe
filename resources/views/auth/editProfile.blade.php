@extends('layouts.login')

@section('content')
    <div class="container">
        <h3 class="mb-1">Edit Profile</h3>
        <div class="row">
            <form action="{{ route('editProfile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row col-md-12">
                    <div class="col-md-12">
                        <label for="">Change your Name</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control @error('name') is-invalid @enderror" placeholder="Change Your Name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="">Change your Phone Number</label>
                        <input type="number" value="{{ $profile[0]->phoneNumber }}" name="phoneNumber" class="form-control @error('phoneNumber') is-invalid @enderror" placeholder="Change Your Name">
                        @error('phoneNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div> 
                    <div class="col-md-12">
                        <label for="">Change your Profile Image</label>
                        <input type="file" name="image" value="" class="form-control @error('image') is-invalid @enderror" placeholder="Change Your Name">

                        @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3"> 
                        <button type="submit" class="btn btn-primary">Edit Profile</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
@endsection