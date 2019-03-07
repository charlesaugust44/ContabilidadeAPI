<?php
/**
 *  Create a password hash
 *
 * @param $password
 * @return string
 */
function hash_password($password)
{
    return hash('sha256', sha1(md5($password)));
}


/**
 * Generate a random string.
 *
 * @param int $str_length
 * @return string
 */

function generate_string($str_length = 22)
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*()+=}]{[|~?><';
    $permitted_chars = str_shuffle($permitted_chars);
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for ($i = 0; $i < $str_length; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

/**
 * Generate JWT token and signature password
 *
 * @param $payload
 * @return array
 */

function generate_token($payload)
{
    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    $header = json_encode($header);
    $payload = json_encode($payload);

    $header = base64_encode($header);
    $payload = base64_encode($payload);

    $secret = generate_string(60);
    $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
    $signature = base64_encode($signature);

    $token = "$header.$payload.$signature";

    return [$token, $secret];
}

/**
 * Check token expiration
 *
 * @param $payload
 * @return bool
 * @throws Exception
 */

function token_expiration($payload)
{
    $pl = json_decode(base64_decode($payload));

    if (!isset($pl->exp))
        return false;

    $now = new DateTime();
    $exp = new DateTime();

    $exp->setTimestamp($pl->exp);

    return $now >= $exp;
}