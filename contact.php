<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /#contact');
    exit;
}

$honeypot = trim((string)($_POST['website'] ?? ''));
$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($honeypot !== '' || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
    header('Location: /?sent=0#contact');
    exit;
}

$safeName = preg_replace('/[\r\n]+/', ' ', $name);
$safeEmail = preg_replace('/[\r\n]+/', '', $email);
$subject = 'Konsultasi website baru dari ' . $safeName;
$body = "Nama: {$safeName}\nEmail: {$safeEmail}\n\nKebutuhan:\n{$message}";
$headers = "From: Webkubator Website <no-reply@webkubator.com>\r\nReply-To: {$safeEmail}\r\nContent-Type: text/plain; charset=UTF-8\r\n";
$sent = @mail('webkubator@gmail.com', $subject, $body, $headers);

header('Location: /?sent=' . ($sent ? '1' : '0') . '#contact');
exit;

