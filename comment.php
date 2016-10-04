<?php

// ログイン画面へのアクセス
$url = 'https://secure.nicovideo.jp/secure/login';
$data = ['mail_tel' => 'km.km.kzkz00@gmail.com', 
         'password' => 'tvrsvr777nc'
         ];

$live_id = '277981463';


$response = file_get_contents($url);

// クッキーを取得
// file_get_contents後に$http_response_headerにレスポンスヘッダが格納されているのでここからクッキー情報を取得
$cookies = array();
foreach ($http_response_header as $v) {
    list($key, $value) = explode(":", $v);
    if ($key == "Set-Cookie") {
        $cookies[] = $value;
    }
}

// CookieからSessionIdを取得                            
$session_id = "";
foreach ($cookies as $v) {
    if (preg_match("/SESSIONID=(.+); /", $v)) {
        $session_id = $v;
    }
}


// 2.  ログイン処理
$method = "POST";

$request = array(
    "user" => $user,
    "password" => $password,
);

$query = http_build_query($request, "", "&");

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

$response = file_get_contents($url, false, stream_context_create($context));











// POST用関数
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
//                       'header'=>"Content-Type: application/x-www-form-urlencoded\r\nContent-Length: $data_len\r\n",
//                       'content'=>$data_url)
//                   )
//               )
//             ),
//       'headers'=> $http_response_header
//   );
// }

// // 送信
// $result = http_post($url, $data);

// var_dump($result);






