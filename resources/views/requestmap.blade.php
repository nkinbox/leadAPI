@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>{{$form->name}} Request Map</h1>
    <form method="POST" action="{{$action}}">
        {{csrf_field()}}
        @if($method != "POST")
        <input type="hidden" name="_method" value="{{$method}}">
        <input type="hidden" name="id" value="{{$row?$row->id:""}}">
        @endif
        <input type="hidden" name="form_request_id" value="{{$row?$row->form_request_id:$form->id}}">
        <div class="form-group">
            <label>CRM Table:</label>
            <select name="crm_table_id">
                @foreach ($tables as $table)
                    <option value="{{$table->id}}"{{($row)?($row->crm_table_id == $table->id)?' selected':'':''}}>{{$table->name}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-outline-primary">{{$operation}} Map</button>
    </form>
    <hr>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Map</th>
            <th scope="col">Access-Token</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr class="{{(!count($row->FormRequestRoute))?'bg-danger text-white':''}}">
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$row->FormRequest->name}} - {{$row->CrmTable->name}}</td>
            <td>{{$row->access_token}}</td>
            <td>
                <a href="{{route('requestRoute', ["map_id" => $row->id])}}">Map Fields</a> /
                <a href="{{route('requestMap', ["form_id" => $row->form_request_id, "operation" => "edit", "id" => $row->id])}}">Edit</a> /
                <a href="{{route('requestMap', ["form_id" => $row->form_request_id, "operation" => "delete", "id" => $row->id])}}">Delete</a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection