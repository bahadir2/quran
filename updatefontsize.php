<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['fontSize'])) {
    $_SESSION['fontSize'] = intval($data['fontSize']);
}
?>