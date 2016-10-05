<?php

// ログイン画面URL、初め、URLが間違っていて、ログインできていなかったので要注意.
$url = 'https://account.nicovideo.jp/api/v1/login?show_button_twitter=1&site=niconico&show_button_facebook=1&next_url=';

$live_id = '';


//ニコニコに登録してるメルアド
$mail_tel = '';
// ニコニコに登録してるパスワード
$password = '';

$cookies = [];

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
    "Cookie: " . "Cookie: " . $session_id, // ★セッション管理ではセッションIDをクッキーにいれることがほとんどなので一緒に送信
    "User-Agent: " . "hogehoge", // UserAgentで弾いてるサイトもあるので必要なら適当にセット
);

$context = array(
    "http" => array(
        "method" => $method,
        "header" => implode("\r\n", $header),
        "content" => $query,
    )
);


// クッキーを取得
// file_get_contents後に$http_response_headerにレスポンスヘッダが格納されているのでここからクッキー情報を取得

$response = file_get_contents($url, false, stream_context_create($context));

// 存在しないURLにリクエストを出したとき、エラー表示
if($http_response_header[0] == 'HTTP/1.1 404 Not Found'){
        print '404 Not Foundです。';
}

var_dump($http_response_header);


foreach ($http_response_header as $v) {

//":"の形以外は飛ばす
    // if (0 == strcmp($v, 'HTTP/1.1 302 Found' || 0 == strcmp($v, 'HTTP/1.1 200 OK'))) {
    //     continue;
    // }


    list($key, $value) = explode(':', $v);

// var_dump(list($key, $value));


    if ($key == 'Set-Cookie') {
        $cookies[] = $value;
    }
}

var_dump($cookies);

// CookieからSessionIdを取得                            

foreach ($cookies as $v) {
    if (preg_match('/user_session=user_session_=(\w+)/', $v, $session_id)) {
    }
}


var_dump($session_id);




// // POST用関数
// function http_post ($url, $data)
// {
//   $data_url = http_build_query ($data);
//   $data_len = strlen ($data_url);
 
//   return array (
//         'content'=>  file_get_contents (
//             $url,
//             false,
//             stream_context_create (
//               array ('http' =>
//                   array (
//                       'method'=>'POST',
//                       'header'=>'Content-Type: application/x-www-form-urlencoded\r\nContent-Length: $data_len\r\n',
//                       'content'=>$data_url)
//                   )
//               )
//             ),
//         'headers'=> $http_response_header
//   );

// }

// // 送信
// $result = http_post($url, $data);








