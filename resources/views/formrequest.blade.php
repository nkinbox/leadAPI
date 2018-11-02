@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>Form Submit Requests</h1>
    <form method="POST" action="{{$action}}">
        {{csrf_field()}}
        @if($method != "POST")
        <input type="hidden" name="_method" value="{{$method}}">
        <input type="hidden" name="id" value="{{$row?$row->id:""}}">
        @endif

        <div class="form-group">
            <label>Form Name:</label>
            <input type="text" autocomplete="off" class="form-control form-control-sm" name="name" value="{{$row?$row->name:""}}">
        </div>

        <div class="form-group">
            <label>Authorize:</label>
            <select name="authorized">
                <option value="0"{{$row?($row->authorized)?'':' selected':""}}>Disallow</option>
                <option value="1"{{$row?($row->authorized)?' selected':'':""}}>Allow</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline-primary">{{$operation}} Request</button>
    </form>
    <hr>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Authorized</th>
            <th scope="col">Map</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr class="{{(!$row->name || !$row->authorized)?'bg-danger text-white':((count($row->Maps))?'':'bg-warning')}}">
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$row->name}}</td>
            <td>{{($row->authorized)?"Yes":"No"}}</td>
            <td><a href="{{route('requestMap', ["form_id" => $row->id])}}">Map ({{count($row->Maps)}})</a></td>
            <td>
                <a href="{{route('formField', ["form_id" => $row->id])}}">Fields</a> /
                <a href="{{route('formRequest', ["operation" => "edit", "id" => $row->id])}}">Edit</a> /
                <a href="{{route('formRequest', ["operation" => "delete", "id" => $row->id])}}">Delete</a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection