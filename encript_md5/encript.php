<?php
function openCypher($action = 'encrypt', $string = false)
{
    $action = trim($action);
    $output = false;
    $myKey = 'oW%c76+jb2';
    $myIV = 'A)2!u467a^';
    $encrypt_method = 'AES-256-CBC';
    $secret_key = hash('sha256', $myKey);
    $secret_iv = substr(hash('sha256', $myIV), 0, 16);
    if ($action && ($action == 'encrypt' || $action == 'decrypt') && $string) {
        $string = trim(strval($string));
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $secret_key, 0, $secret_iv);
        }
        ;

        if ($action == 'decrypt') {
            $output = openssl_decrypt($string, $encrypt_method, $secret_key, 0, $secret_iv);
        }
        ;
    }
    ;

    return $output;
}
function Encriptar($value)
{
    $myText_encrypted = openCypher('encrypt', $value);
    return $myText_encrypted;
}
function Desencriptar($value)
{
    $myText_decrypted = openCypher('decrypt', $value);
    return $myText_decrypted;
}