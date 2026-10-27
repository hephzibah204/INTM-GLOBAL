<?php
$qs = $_SERVER['QUERY_STRING'] ?? '';
$target = './verify.html' . ($qs !== '' ? '?' . $qs : '');
header('Location: ' . $target, true, 302);
exit;
?>
