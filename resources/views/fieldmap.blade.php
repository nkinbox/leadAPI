@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>{{$map->FormRequest->name}} - {{$map->CrmTable->name}} <small>CRM Route</small></h1>
    <form method="POST" action="{{route('requestRoute.create')}}">
        {{csrf_field()}}
        <input type="hidden" name="form_map_id" value="{{$map->id}}">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Form Field</th>
                <th scope="col">Table Column</th>
                </tr>
            </thead>
            <tbody>
                @foreach($map->FormRequest->Fields as $field)
                <tr>
                <th scope="row">{{$loop->iteration}}</th>
                <td>{{$field->name}}</td>
                <td>
                    <input type="hidden" name="form_field_id[{{$loop->index}}]" value="{{$field->id}}">
                    <select name="crm_column_id[{{$loop->index}}]">
                        @foreach ($map->CrmTable->Columns as $column)
                        <option value="{{$column->id}}"{{(isset($fieldmap[$field->id]))?(($fieldmap[$field->id] == $column->id)?' selected':''):''}}>{{$column->name}}</option>
                        @endforeach
                    </select>
                </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-outline-primary">Make Route Map</button>
    </form>
</div>
@endsection