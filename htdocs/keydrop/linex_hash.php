<?php

function linex_generate_salt() {
    return bin2hex(random_bytes(8));
}

function linex_hash($data, $salt = null, $encoding = 'ascii', $size = 128, $iterations = 10) {
    $encoding = strtolower($encoding);
    
    $chars = [
        'ascii' => implode('', array_map('chr', range(33, 126))),
        'unicode' => implode('', array_map('chr', range(33, 9999))),
        'base64' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/+',
        'hex' => '0123456789ABCDEF'
    ];

    if (!array_key_exists($encoding, $chars)) {
        throw new Exception("The format \"$encoding\" is not supported by Linex Hash function.");
    }

    $chars_string = $chars[$encoding];
    $chars_len = strlen($chars_string);

    $salt = $salt ?? linex_generate_salt();
    if (strlen($salt) !== 16) {
        throw new Exception("The salt has to be 16 characters long.");
    }

    $pbkdf2_output = hash_pbkdf2('sha512', $data, $salt, $iterations, $size * 4, true);
    
    $result = '';
    for ($i = 0; $i < $size; $i++) {
        $byte_offset = $i * 4;
        if ($byte_offset < strlen($pbkdf2_output)) {
            $value = 0;
            for ($j = 0; $j < 4 && $byte_offset + $j < strlen($pbkdf2_output); $j++) {
                $value = ($value << 8) | ord($pbkdf2_output[$byte_offset + $j]);
            }
            
            $char_index = abs($value) % $chars_len;
            $result .= $chars_string[$char_index];
        } else {
            $extra_salt = md5($salt . $i);
            $extra_hash = hash_hmac('sha256', $data . $i, $extra_salt, true);
            $value = 0;
            for ($j = 0; $j < 4 && $j < strlen($extra_hash); $j++) {
                $value = ($value << 8) | ord($extra_hash[$j]);
            }
            $char_index = abs($value) % $chars_len;
            $result .= $chars_string[$char_index];
        }
    }
    
    $str_iterations = str_pad(strval($iterations), 4, '0', STR_PAD_LEFT);
    return $str_iterations . $salt . $result;
}

function linex_verify($data, $hashed, $encoding = 'ascii') {
    try {
        $iterations = intval(substr($hashed, 0, 4));
        $salt = substr($hashed, 4, 16);
        $size = strlen($hashed) - 20;
        $correct_hash = linex_hash($data, $salt, $encoding, $size, $iterations);
        return $correct_hash === $hashed;
    } catch (Exception) {
        throw new Exception("Hash is invalid!");
    }
}
?>