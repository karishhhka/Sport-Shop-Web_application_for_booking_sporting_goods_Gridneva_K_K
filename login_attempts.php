<?php
define('ATTEMPT_LOG_FILE', __DIR__ . '/login_attempts.log');
define('MAX_ATTEMPTS', 5); // Максимум попыток
define('BLOCK_TIME', 300); // Время блокировки в секундах (5 минут)

function log_login_attempt($email, $ip) {
    $attempts = get_login_attempts($ip);
    $now = time();

    $newAttempts = array_filter($attempts, function ($time) use ($now) {
        return $time > $now - BLOCK_TIME;
    });

    $newAttempts[] = $now;

    file_put_contents(ATTEMPT_LOG_FILE, json_encode([
        $ip => $newAttempts
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function is_blocked($ip) {
    $attempts = get_login_attempts($ip);
    $now = time();


    $recentAttempts = array_filter($attempts, function ($time) use ($now) {
        return $time > $now - BLOCK_TIME;
    });

    if (count($recentAttempts) >= MAX_ATTEMPTS) {
        return true;
    }

    return false;
}

function get_login_attempts($ip) {
    if (!file_exists(ATTEMPT_LOG_FILE)) {
        return [];
    }

    $logs = file(ATTEMPT_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];

    foreach ($logs as $line) {
        $entry = json_decode($line, true);
        if (is_array($entry)) {
            $data += $entry;
        }
    }

    return $data[$ip] ?? [];
}