@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="content">
                        <div class="title m-b-md">
                            您好，請點擊下列連動按鈕，後續會發送Line通知給您
                        </div>

                        <div class="links">
                            <button onclick="oAuth2();"> 連結到 LineNotify 按鈕 </button>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        function oAuth2() {
            var URL = 'https://notify-bot.line.me/oauth/authorize?';
            URL += 'response_type=code';
            URL += '&client_id=vwyI9hxuPjGC72KUrGDaiF';
            URL += '&redirect_uri=https://12cb-111-253-159-131.ngrok.io/api/callback';
            URL += '&scope=notify';
            URL += '&state=NO_STATE';
            URL += '&uid=0857';
            window.location.href = URL;
        }
    </script>
@endsection
