@extends('layouts.main')

@section('content')
    <h2>Hasil Lomba</h2>
    <div class="table-responsive">
        <table id="dataTable" class="table table-hover">
            <thead>
            <tr><th>Pos</th><th>Nama Atlet</th><th>Klub</th><th>Waktu</th><th>Status</th></tr>
            </thead>
            <tbody>
            <tr><td>1</td><td>Budi Santoso</td><td>Club A</td><td>00:58.90</td><td>OK</td></tr>
            <tr><td>2</td><td>Andi Saputra</td><td>Club B</td><td>01:00.20</td><td>OK</td></tr>
            <tr><td>DQ</td><td>Tono Wijaya</td><td>Club C</td><td>-</td><td>False Start</td></tr>
            </tbody>
        </table>
        <button class="btn btn-outline-primary">Export PDF</button>
        <button class="btn btn-outline-success">Export CSV</button>
    </div>
@endsection