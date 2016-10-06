<?php

// ログイン画面URL、初め、URLが間違っていて、ログインできていなかったので要注意.
$login_url = 'https://account.nicovideo.jp/api/v1/login?show_button_twitter=1&site=niconico&show_button_facebook=1&next_url=';

//プレイヤー情報を得るためのリクエストURL
$ply_sts_url = 'http://live.nicovideo.jp/api/getplayerstatus?v=';

// 放送ID
$live_id = '';


//ニコニコに登録してるメルアド
$mail_tel = '';
// ニコニコに登録してるパスワード
$password = '';

$cookies = array();

$session_id = '';

//ログイン処理
$method = 'POST';

$request = array('mail_tel' => $mail_tel, 
            'password' => $password
);

$query = http_build_query($request, '', '&');

//リクエストヘッダ
$header = array(
    "Content-Type: application/x-www-form-urlencoded",
    "Content-Length: " . strlen($query),
    "Cookie: " . $session_id, 
    "User-Agent: " . "hogehoge", 
);

$context = array(
    "http" => array(
        "method" => $method,
        "header" => implode("\r\n", $header),
        "content" => $query,
    )
);



$response = file_get_contents($login_url, false, stream_context_create($context));

// 存在しないURLにリクエストを出したとき、エラー表示
if($http_response_header[0] == 'HTTP/1.1 404 Not Found'){
        print '404 Not Foundです。';
}

// var_dump($http_response_header);

// クッキーを取得
// file_get_contents後に$http_response_headerにレスポンスヘッダが格納されているのでここからクッキー情報を取得
foreach ($http_response_header as $v) {

//":"の形以外は飛ばす
    // if (0 == strcmp($v, 'HTTP/1.1 302 Found' || 0 == strcmp($v, 'HTTP/1.1 200 OK'))) {
    //     continue;
    // }


    list($key, $value) = explode(':', $v);

    if ($key == 'Set-Cookie') {
        $cookies[] = $value;
    }
}

// var_dump($cookies);

// CookieからSessionIdを取得                            
foreach ($cookies as $v) {

    if (preg_match('/user_session=user_session/', $v)) {
        // /user_session=user_session_が含まれる場合
        //user_session=user_session_~;までが必要


        $session_id = mb_strstr(trim($v),' ',true);

    }
}


// var_dump($session_id);

//ログイン後にやりたい処理
// コメントサーバー情報の取得
$method = 'POST';

$request = array();

$query = http_build_query($request, '', '&');

//リクエストヘッダ
$header = array(
    "Content-Type: application/x-www-form-urlencoded",
    "Content-Length: " . strlen($query),
    "Cookie: " . $session_id, 
    "User-Agent: " . "hogehoge", 
);

$context = array(
    "http" => array(
        "method" => $method,
        "header" => implode("\r\n", $header),
        "content" => $query,
    )
);



$response = file_get_contents($ply_sts_url.$live_id, false, stream_context_create($context));

//XMLをオブジェクトに変換
$xmlObj = simplexml_load_string($response);

//オブジェクトを連想配列に変換
$xmlAry = json_decode( json_encode($xmlObj),true);

// コメントサーバーURL
$addr = $xmlAry['ms']['addr'];
// ポート
$port = $xmlAry['ms']['port'];
//スレッド
$thread = $xmlAry['ms']['thread'];







$request = array('port' => $port,
                'thread' => $thread
                );
$method = 'POST';

$query = http_build_query($request, '', '&');

//リクエストヘッダ
$header = array(
    "Content-Type: application/x-www-form-urlencoded",
    "Content-Length: " . strlen($query),
    "Cookie: " . $session_id, 
    "User-Agent: " . "hogehoge", 
);

$context = array(
    "http" => array(
        "method" => $method,
        "header" => implode("\r\n", $header),
        "content" => $query,
    )
);





$response = file_get_contents($addr, false, stream_context_create($context));



var_dump($response);








