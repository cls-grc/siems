<?php

function siemsTwoFactorColumnsReady() {
    global $pdo;

    if (!isset($pdo)) {
        return false;
    }

    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = 'users'
              AND column_name IN ('two_factor_enabled', 'two_factor_secret')
        ");
        $stmt->execute();
        $ready = ((int) $stmt->fetchColumn()) === 2;
    } catch (Throwable $e) {
        $ready = false;
    }

    return $ready;
}

function siemsBase32Encode($input) {
    if ($input === '') {
        return '';
    }

    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $binary = '';
    $length = strlen($input);

    for ($index = 0; $index < $length; $index++) {
        $binary .= str_pad(decbin(ord($input[$index])), 8, '0', STR_PAD_LEFT);
    }

    $chunks = str_split($binary, 5);
    $encoded = '';

    foreach ($chunks as $chunk) {
        if (strlen($chunk) < 5) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
        }
        $encoded .= $alphabet[bindec($chunk)];
    }

    return $encoded;
}

function siemsBase32Decode($input) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $input = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input ?? ''));
    if ($input === '') {
        return '';
    }

    $binary = '';
    $length = strlen($input);
    for ($index = 0; $index < $length; $index++) {
        $position = strpos($alphabet, $input[$index]);
        if ($position === false) {
            return '';
        }
        $binary .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
    }

    $bytes = str_split($binary, 8);
    $decoded = '';
    foreach ($bytes as $byte) {
        if (strlen($byte) === 8) {
            $decoded .= chr(bindec($byte));
        }
    }

    return $decoded;
}

function siemsGenerateTwoFactorSecret($length = 20) {
    return siemsBase32Encode(random_bytes($length));
}

function siemsBuildOtpAuthUri($label, $secret, $issuer = 'SIEMS') {
    return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label)
        . '?secret=' . rawurlencode($secret)
        . '&issuer=' . rawurlencode($issuer)
        . '&algorithm=SHA1&digits=6&period=30';
}

function siemsNormalizeOtpCode($code) {
    return preg_replace('/\D+/', '', trim((string) $code));
}

function siemsGenerateTotpCode($secret, $timestamp = null, $period = 30, $digits = 6) {
    $timestamp = $timestamp ?? time();
    $counter = pack('N*', 0) . pack('N*', (int) floor($timestamp / $period));
    $secretKey = siemsBase32Decode($secret);

    if ($secretKey === '') {
        return null;
    }

    $hash = hash_hmac('sha1', $counter, $secretKey, true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $binary = (
        ((ord($hash[$offset]) & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) << 8) |
        (ord($hash[$offset + 3]) & 0xFF)
    );

    $otp = $binary % (10 ** $digits);
    return str_pad((string) $otp, $digits, '0', STR_PAD_LEFT);
}

function siemsVerifyTotpCode($secret, $code, $window = 1) {
    $normalizedCode = siemsNormalizeOtpCode($code);
    if (strlen($normalizedCode) !== 6) {
        return false;
    }

    $now = time();
    for ($offset = -$window; $offset <= $window; $offset++) {
        $candidate = siemsGenerateTotpCode($secret, $now + ($offset * 30));
        if ($candidate !== null && hash_equals($candidate, $normalizedCode)) {
            return true;
        }
    }

    return false;
}

