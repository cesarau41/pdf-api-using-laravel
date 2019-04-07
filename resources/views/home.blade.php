@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @guest
                    @else
                        <p>{{Auth::user()->name}}, your api token is <br><strong>{{Auth::user()->api_token}}</strong></p><br><hr>
                        <h1>Your files</h1>
                        <ul class="list-group">
                        @foreach(Auth::user()->pdf_files as $file)
                            <li class="list-group-item"><a href="storage/pdf_files/{{$file->filename}}">{{$file->title}}</a></li>
                        @endforeach
                        </ul>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
