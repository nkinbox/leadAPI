@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">API CURL CODE</div>

                <div class="panel-body">
                    <hr>
<pre>
&lt;?php
    $url = 'https://www.tripclues.net/leadAPI/public/api/insert';
    $fields = array(
        'field' => $_POST['field'],
    );
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    $result = curl_exec($ch);
    curl_close($ch);
?&gt;
</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
