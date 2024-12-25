<?php
require 'db.php';

function getUser($chat_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE chat_id = ?;");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createUser($chat_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO users (chat_id) VALUES (?);");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
}

function updateBalance($chat_id, $amount) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE chat_id = ?;");
    $stmt->bind_param("di", $amount, $chat_id);
    $stmt->execute();
}

function getBalance($chat_id) {
    $user = getUser($chat_id);
    return $user ? $user['balance'] : null;
}
?>
