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


// var_dump($response);

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


//送信するメッセージ
$msg = '<thread thread="'.$thread.'" version="(20061206|20090904)" res_from="-100"/>';

$res = '';

// TCP/IP ソケット作成
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if ($socket == true) {

    // 送信タイムアウト時間の設定(10秒)
    // $timeout = array('sec' => 10, 'usec' => 0);
    // socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeout);


    // ソケット接続
    $result = socket_connect($socket,$addr,$port);

    if ($result == true) {

        $result = socket_write($socket, $msg, strlen($msg));

        if ($result == true){

            //PHP_EOL:改行
            $res = "送信しました。".PHP_EOL;






        }else{

            //socket_strerror:ソケットエラーの内容を文字列として返す
            //socket_last_error:ソケットの直近のエラーを返す
            $res = "socket_write() 失敗: ".socket_strerror(socket_last_error($socket)).PHP_EOL;
            
        }



    }else{

        $res = "socket_connect() 失敗: ".socket_strerror(socket_last_error($socket)).PHP_EOL;


    }

}else{

    $res = "socket_create() 失敗: ".socket_strerror(socket_last_error()).PHP_EOL;


}


var_dump($res);

// $buf = 'This is my buffer.';

// if (false !== ($bytes = socket_recv($socket, $buf, 2048, MSG_WAITALL))) {

//     echo "Read $bytes bytes from socket_recv(). Closing socket...";
// } else {
//     echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($socket)) . "\n";
// }


$data = socket_read($socket, 200);


var_dump($data);






print('aaaaaaaaa');
// var_dump($bytes);

// var_dump($buf);




// $request = array('port' => $port,
//                 'thread' => $thread
//                 );
// $method = 'POST';

// $query = http_build_query($request, '', '&');

// //リクエストヘッダ
// $header = array(
//     "Content-Type: application/x-www-form-urlencoded",
//     "Content-Length: " . strlen($query),
//     "Cookie: " . $session_id, 
//     "User-Agent: " . "hogehoge", 
// );

// $context = array(
//     "http" => array(
//         "method" => $method,
//         "header" => implode("\r\n", $header),
//         "content" => $query,
//     )
// );





// $response = file_get_contents($addr, false, stream_context_create($context));











