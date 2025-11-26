@extends('layouts.main')

@section('content')
    <h1 class="mb-4">Dashboard</h1>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Atlet</h5>
            <p class="card-text">Kelola data atlet terdaftar</p>
            <a href="{{ route('atlet.index') }}" class="btn btn-primary">Lihat</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Klub</h5>
            <p class="card-text">Kelola data klub & tim</p>
            <a href="{{ route('club.index') }}" class="btn btn-primary">Lihat</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Kompetisi</h5>
            <p class="card-text">Daftar & kelola kompetisi</p>
            <a href="{{ route('competition.index') }}" class="btn btn-primary">Lihat</a>
          </div>
        </div>
      </div>
    </div>
@endsection
