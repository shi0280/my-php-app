<?php
class mailService
{
    public static function send($email, $content)
    {
        mb_language("ja");
        mb_internal_encoding("UTF-8");

        $from = 'from@todo_app.com';
        $header = "From: $from \n";
        $header = $header
            . "MIME-Version: 1.0\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        $subject = 'アカウント仮登録';
        $body = $content;

        $body = mb_convert_encoding($body, "UTF-8");

        $ret = mb_send_mail($email, $subject, $body, $header);
    }
}
