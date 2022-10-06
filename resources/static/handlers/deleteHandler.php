<?php

$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die('Unable to create socket');

$result = socket_connect($sock, '127.0.0.1', 8080) or die('Unable to bind socket');

$email = $_POST['delete'];

$message = "DELETE /users/delete/$email HTTP1.1";
socket_write($sock, $message, strlen($message));

socket_close($sock);
header('Location: http://localhost/index.php');
die();
