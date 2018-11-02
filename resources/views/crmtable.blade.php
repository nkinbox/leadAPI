@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>CRM Table</h1>
    <form method="POST" action="{{$action}}">
        {{csrf_field()}}
        @if($method != "POST")
        <input type="hidden" name="_method" value="{{$method}}">
        <input type="hidden" name="id" value="{{$row?$row->id:""}}">
        @endif


        <div class="form-group">
            <label>Table Name:</label>
            <input type="text" autocomplete="off" class="form-control form-control-sm" name="name" value="{{$row?$row->name:""}}">
        </div>

        <button type="submit" class="btn btn-outline-primary">{{$operation}} Table</button>
    </form>
    <hr>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$row->name}}</td>
            <td>
                <a href="{{route('column', ["table_id" => $row->id])}}">Columns</a> /
                <a href="{{route('table', ["operation" => "edit", "id" => $row->id])}}">Edit</a> /
                <a href="{{route('table', ["operation" => "delete", "id" => $row->id])}}">Delete</a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection