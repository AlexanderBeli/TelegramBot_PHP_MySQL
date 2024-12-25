<?php
require 'functions.php';

$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $message_text = trim($update['message']['text']);

    // Проверяем, существует ли пользователь
    $user = getUser($chat_id);
    
    if (!$user) {
        // Если нет, создаем нового пользователя
        createUser($chat_id);
        sendMessage($chat_id, "Добро пожаловать! У вас на счету $0.00.");
        return;
    }

    // Проверяем, является ли сообщение числом
    if (is_numeric(str_replace(',', '.', $message_text))) {
        // Преобразуем текст в число с плавающей точкой
        $amount = floatval(str_replace(',', '.', $message_text));

        // Проверка на отрицательное значение
        if ($amount < 0) {
            // Проверка на недостаток средств
            if (getBalance($chat_id) + $amount < 0) {
                sendMessage($chat_id, "Ошибка: недостаточно средств на счете.");
            } else {
                updateBalance($chat_id, $amount);
                sendMessage($chat_id, "С вашего счета списано " . abs($amount) . ". Остаток: " . getBalance($chat_id));
            }
        } else {
            updateBalance($chat_id, $amount);
            sendMessage($chat_id, "На ваш счет зачислено " . $amount . ". Остаток: " . getBalance($chat_id));
        }
    } else {
        sendMessage($chat_id, "Пожалуйста, введите число для изменения баланса.");
    }
}

function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text);
    file_get_contents($url);
}
?>