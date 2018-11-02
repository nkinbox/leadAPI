@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>{{$table->name}} Columns</h1>
    <form method="POST" action="{{$action}}">
        {{csrf_field()}}
        @if($method != "POST")
        <input type="hidden" name="_method" value="{{$method}}">
        <input type="hidden" name="id" value="{{$row?$row->id:""}}">
        @endif
        <input type="hidden" name="crm_table_id" value="{{$row?$row->crm_table_id:$table->id}}">


        <div class="form-group">
            <label>Column Name:</label>
            <input type="text" autocomplete="off" class="form-control form-control-sm" name="name" value="{{$row?$row->name:""}}">
        </div>

        <div class="form-group">
            <label>Data Type: <small class="text-muted">string, integer etc</small></label>
            <input type="text" autocomplete="off" class="form-control form-control-sm" name="type" value="{{$row?$row->type:"string"}}">
        </div>

        <div class="form-group">
            <label>Max Length:</label>
            <input type="number" autocomplete="off" class="form-control form-control-sm" name="max_length" value="{{$row?$row->max_length:"0"}}">
        </div>

        <div class="form-group">
            <label>Required:</label>
            <select name="required">
                <option value="0"{{$row?($row->required)?'':' selected':""}}>No</option>
                <option value="1"{{$row?($row->required)?' selected':'':""}}>Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Present:</label>
            <select name="sometimes">
                <option value="0"{{$row?($row->sometimes)?'':' selected':""}}>Always</option>
                <option value="1"{{$row?($row->sometimes)?' selected':'':""}}>Sometimes</option>
            </select>
        </div>

        <div class="form-group">
            <label>Default Value: <small class="text-muted">eval(date('Y-m-d')) or eval("") or eval(null) or Value</small></label>
            <input type="text" autocomplete="off" class="form-control form-control-sm" name="default_value" value="{{$row?$row->default_value:""}}">
        </div>

        <button type="submit" class="btn btn-outline-primary">{{$operation}} Column</button>
    </form>
    <hr>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">DataType</th>
            <th scope="col">Required</th>
            <th scope="col">Present</th>
            <th scope="col">Default</th>
            <th scope="col">Max Length</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$row->name}}</td>
            <td>{{$row->type}}</td>
            <td>{{($row->required)?"Yes":"No"}}</td>
            <td><code>{{($row->sometimes)?"Sometimes":"Always"}}</code></td>
            <td><code>{{$row->default_value}}</code></td>
            <td>{{$row->max_length}} Chars</td>
            <td>
                <a href="{{route('column', ["table_id" => $table->id, "operation" => "edit", "id" => $row->id])}}">Edit</a> /
                <a href="{{route('column', ["table_id" => $table->id, "operation" => "delete", "id" => $row->id])}}">Delete</a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection