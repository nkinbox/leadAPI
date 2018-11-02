@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>{{$website->domain}} Request Map</h1>
    {{-- <form method="POST" action="{{$action}}">
        {{csrf_field()}}
        @if($method != "POST")
        <input type="hidden" name="_method" value="{{$method}}">
        <input type="hidden" name="id" value="{{$row?$row->id:""}}">
        @endif
        <div class="form-group">
            <label>Form Request:</label>
            <select name="form_request_id">
                <option value="0">Select Request</option>
                @foreach ($website->FormRequest as $form)
                    <option value="{{$form->id}}"{{($row)?($row->form_request_id == $form->id)?' selected':'':''}}>{{$form->name}}</option>
                @endforeach
            </select>
            {{dd()}}
        </div>
        <div class="form-group">
            <label>Form Request Map:</label>
            <select name="form_map_id">
                <option value="0">Select Map</option>
                @foreach ($FormMap as $map)
                    <option value="{{$map->id}}"{{($row)?($row->form_map_id == $map->id)?' selected':'':''}}>{{$row->FormRequest->name}} - {{$row->CrmTable->name}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-outline-primary">{{$operation}} Map</button>
    </form> --}}
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Request</th>
            <th scope="col">Map</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr class="{{(!$row->form_map_id)?'bg-danger text-white':''}}">
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$row->FormRequest->name}}</td>
            <td>
            <form method="POST" action="{{$action}}">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$row->id}}">
                <select name="form_map_id">
                    <option value="0">Select Request Map</option>
                    @foreach($row->FormRequest->Maps as $map)
                        <option value="{{$map->id}}"{{($map->id == $row->form_map_id)?' selected':''}}>{{$row->FormRequest->name}} - {{$map->CrmTable->name}}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">Update Map</button>
            </form>
            </td>
            <td>
            <form method="POST" action="{{$action}}">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$row->id}}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-sm btn-danger">Delete Map</button>
            </form>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection