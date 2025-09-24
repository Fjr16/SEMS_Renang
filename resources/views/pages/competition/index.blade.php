@extends('layouts.main')

@section('content')
    <h2>Kompetisi & Event</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalComp">+ Tambah Kompetisi</button>

    <div class="table-responsive">
        <table id="dataTable" class="table table-striped">
            <thead>
            <tr><th>#</th><th>Nama Kompetisi</th><th>Tanggal</th><th>Venue</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td><td>Kejuaraan Nasional</td><td>2025-08-12</td><td>Jakarta Aquatic Stadium</td>
                <td><button class="btn btn-info btn-sm">Event</button></td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection