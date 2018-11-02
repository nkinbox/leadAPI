@extends('layouts.app')
@section('content')
<div class="container py-3">
    <h1>Tripclues Websites</h1>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Domain</th>
            <th scope="col">Form Requests</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($websites as $website)
            @php
            $formrequest = $website->FormRequest->count();
            @endphp
            <tr>
            <th scope="row">{{$loop->iteration}}</th>
            <td><a href="http://{{$website->domain}}" target="_blank">{{$website->domain}}</a></td>
            <td class="{{(!$formrequest)?'bg-danger':''}}">{{$formrequest}}</td>
            <td class="{{(in_array($website->id, $unmappedRequest))?'bg-danger':''}}">
                <a href="{{route('websiteMap', ["website_id" => $website->id])}}" class="{{(in_array($website->id, $unmappedRequest))?'text-white':''}}">Request Map</a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection