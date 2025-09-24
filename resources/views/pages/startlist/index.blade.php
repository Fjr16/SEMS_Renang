@extends('layouts.main')

@section('content')
    <h2>Startlist</h2>
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered">
            <thead>
            <tr><th>Heat</th><th>Lane</th><th>Nama Atlet</th><th>Klub</th><th>Seed Time</th></tr>
            </thead>
            <tbody>
            <tr><td>1</td><td>4</td><td>Budi Santoso</td><td>Club A</td><td>00:59.12</td></tr>
            <tr><td>1</td><td>5</td><td>Andi Saputra</td><td>Club B</td><td>01:01.33</td></tr>
            </tbody>
        </table>
    </div>
@endsection