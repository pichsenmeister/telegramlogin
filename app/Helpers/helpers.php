<?php

function generate_access_token() {
    return 'A'.random_str(59);
}

function generate_token() {
    return 'T'.random_str(59);
}

function generate_code() {
    return 'C'.random_str(29);
}

function generate_client_id() {
    return random_number(9);
}

function generate_client_secret() {
    return random_str(20, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-_=$!');
}

function generate_email() {
    return random_str(15);
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function random_number($length, $keyspace = '0123456789')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return intval($str);
}
