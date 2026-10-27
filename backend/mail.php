<?php
function send_mail($to, $subject, $body) {
    if (MAIL_URL !== '') {
        $r = @file_get_contents(MAIL_URL . '?' . http_build_query(['key' => MAIL_KEY, 'to' => $to, 'subject' => $subject, 'body' => $body]),
            false, stream_context_create(['http' => ['timeout' => 15]]));
        if ($r !== false && trim($r) === 'ok') return true;
        if (SMTP_PASS === '') return false;
    }
    $s = @stream_socket_client('ssl://smtp.gmail.com:465', $en, $es, 10);
    if (!$s) return false;
    $cmd = function ($c, $ok) use ($s) {
        if ($c !== null) fwrite($s, $c . "\r\n");
        do { $l = fgets($s, 512); } while ($l !== false && isset($l[3]) && $l[3] === '-');
        return $l !== false && strncmp($l, $ok, 3) === 0;
    };
    $msg = "From: Deepkala Hair Salon <" . SMTP_USER . ">\r\nTo: $to\r\nSubject: $subject\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$body";
    $ok = $cmd(null, '220') && $cmd('EHLO localhost', '250') && $cmd('AUTH LOGIN', '334')
        && $cmd(base64_encode(SMTP_USER), '334') && $cmd(base64_encode(SMTP_PASS), '235')
        && $cmd('MAIL FROM:<' . SMTP_USER . '>', '250') && $cmd("RCPT TO:<$to>", '250')
        && $cmd('DATA', '354') && $cmd($msg . "\r\n.", '250');
    fwrite($s, "QUIT\r\n"); fclose($s);
    return $ok;
}
