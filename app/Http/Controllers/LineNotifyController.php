<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class LineNotifyController extends Controller
{
    public $main_uri = 'https://97helper.com/';

    public function callBack(Request $req)
    {
        $headers = [
			'Content-Type: application/x-www-form-urlencoded',
		];

		$datas = [
			'grant_type'    => 'authorization_code',
			'code'          => $req->code,
            'redirect_uri'  => $this->main_uri.'/api/callback',
			'client_id'     => 'hNxoLbJcAlKHnOJX9LKSi3',
			'client_secret' => '61YkMUtJfSmNekoICdOrouhDFwlzzPEJ14kJXpHfR55',
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL , "https://notify-bot.line.me/oauth/token");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if ($error = curl_error($ch)) {
			die($error);
	    }
		curl_close($ch);
		$response = json_decode($result);

        if ($response->access_token != null) {
            $this->sendMessage($response->access_token, '您好~刷一整排外星人~');
            return redirect()->route('authline', ['token' => $response->access_token]);
        }
    }

    // 綁定會員帳號與Line Token
    public function authLine(Request $req)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $user->line_token = $req->token;
        $user->save();

        return view('authline');
    }

    public function sendMessage($access_token, $mymessage)
    {
        $headers = array(
            'Content-Type: multipart/form-data',
            'Authorization: Bearer '.$access_token.''
        );
        $message = array(
            'message' => $mymessage
        );
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
