<?php

mb_language("ja");
mb_internal_encoding("UTF-8");

$from = 'from@example.com';
$header = "From: $from \n";
$header = $header
    . "MIME-Version: 1.0\r\n"
    . "Content-Transfer-Encoding: 8bit\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n";
$to   = 'to@example.com';
$subject = 'テストメール';
$body = 'メールの送信テストです。';

$body = mb_convert_encoding($body, "UTF-8");

$ret = mb_send_mail($to, $subject, $body, $header);
var_dump($ret);
echo $body;
echo $subject;
