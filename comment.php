<?php

define('NICONICO_MAILADDRESS','');
define('NICONICO_PASSWORD', '');
define('NICONICO_LOGIN_URL', 'https://account.nicovideo.jp/api/v1/login?show_button_twitter=1&site=niconico&show_button_facebook=1&next_url=');
define('NICONICO_GETPLAYERSTATUS_URL', 'http://live.nicovideo.jp/api/getplayerstatus?v=');


// 放送ID
$live_id = 'lv';

$cookies = array();

$session_id = '';

//ログイン処理
$method = 'POST';

$request = array('mail_tel' => NICONICO_MAILADDRESS, 
            'password' => NICONICO_PASSWORD
);

$query = http_build_query($request, '', '&');

//リクエストヘッダ
$header = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($query),
    'Cookie: ' . $session_id, 
    'User-Agent: ' . 'hogehoge', 
);

$context = array(
    'http' => array(
        'method' => $method,
        'header' => implode('\r\n', $header),
        'content' => $query,
    )
);

$response = file_get_contents(NICONICO_LOGIN_URL, false, stream_context_create($context));

// 存在しないURLにリクエストを出したとき、エラー表示
if($http_response_header[0] == 'HTTP/1.1 404 Not Found'){
        print '404 Not Foundです。';
}

var_dump($http_response_header);

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

var_dump($cookies);

// CookieからSessionIdを取得                            
foreach ($cookies as $v) {

    if (preg_match('/user_session=user_session/', $v)) {
        // /user_session=user_session_が含まれる場合
        //user_session=user_session_~;までが必要

        $session_id = mb_strstr(trim($v),' ',true);
    }
}

var_dump($session_id);

//ログイン後にやりたい処理
// コメントサーバー情報の取得
$method = 'POST';

$request = array();

$query = http_build_query($request, '', '&');

//リクエストヘッダ
$header = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($query),
    'Cookie: ' . $session_id, 
    'User-Agent: ' . 'hogehoge', 
);

//implodeの第一引数を''で囲むとなぜか、responseがなぜかnotloginが返ってくる
$context = array(
    'http' => array(
        'method' => $method,
        'header' => implode("\r\n", $header),
        'content' => $query,
    )
);

$response = file_get_contents(NICONICO_GETPLAYERSTATUS_URL.$live_id, false, stream_context_create($context));

var_dump($response);

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

var_dump($xmlAry);

//送信するメッセージ
// res_fromは新しい順に何件前までを取得するかを指定 -1から-1000まで
// res_fromに不正な数値が入力された場合は、-250が設定される。
// 投稿者コメントを取得する時は、これにfork="1"を追加する:
$msg_format = '<thread thread="%s" version="(20061206|20090904)" res_from="-10" scores="1"/>\0';


$msg = sprintf($msg_format,(string)$thread);




/*
以下、ソケット関数を用いている
*/

$res = '';

// TCP/IP ソケット作成
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if ($socket == true) {

    // ソケット接続
    $connect_result = socket_connect($socket,$addr,$port);

var_dump($connect_result);


    if ($connect_result === true) {

        $write_result = socket_write($socket, $msg, strlen($msg));
var_dump($write_result);

        if ($write_result == true){

            $res = '送信しました。'.PHP_EOL;

        }else{

            //socket_strerror:ソケットエラーの内容を文字列として返す
            //socket_last_error:ソケットの直近のエラーを返す
            $res = 'socket_write() 失敗: '.socket_strerror(socket_last_error($socket)).PHP_EOL;
            
        }

    }else{

        $res = 'socket_connect() 失敗: '.socket_strerror(socket_last_error($socket)).PHP_EOL;
    }

}else{

    $res = 'socket_create() 失敗: '.socket_strerror(socket_last_error()).PHP_EOL;


}
var_dump($res);


$buf = 'This is my buffer.';

if (false !== ($bytes = socket_recv($socket, $buf, 2048, MSG_DONTWAIT))) {
    echo "Read $bytes bytes from socket_recv(). Closing socket...";
} else {
    var_dump($bytes);
    echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($socket)) . "\n";
}

socket_close($socket);

var_dump($buf);




// f~関数使ったやりかた


//とりあえずホスト名からソケットをオープンする
// $socket = fsockopen((string)$addr,(string)$port,$errno,$errstr,30);

// stream_set_blocking($socket, 0);


// //オープンしたソケットに上記のリクエストを書き込む(※送信する)
// fwrite($socket,$msg);


//  $buffer = '';

// while(1){

//     $buffer .= fread($socket, 1024);

// }

//  fclose($socket);

// var_dump($errstr);



