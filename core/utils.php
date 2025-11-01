<?php
function generateJwtToken($payload)
{
    $secret = getenv('JWT_SECRET_KEY');
    // Header
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');

    // Payload
    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24 * 14); // expires in 14 days
    $base64UrlPayload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret, true);
    $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');


    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}


function verifyJwtToken($token)
{
    $secret = getenv('JWT_SECRET_KEY');

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    list($header, $payload, $signature) = $parts;

    $checkSig = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true)), '+/', '-_'), '=');

    if (!hash_equals($checkSig, $signature)) {
        return null; // Invalid signature
    }

    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    // Check expiry
    if (!isset($data['exp']) || $data['exp'] < time()) {
        return null; // Expired
    }

    return $data; // Valid token
}

function protectRoute()
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response(401, 'Unauthorized to access this resource');
    }

    global $pdo;
    $session = new UserSession($pdo, null, $headers['Authorization']);

    $user_id = $session->verify_token();
    if ($user_id) {
        $user = new User($pdo);
        $user = $user->get($user_id);
        return $user;
    }

    // $token = str_replace('Bearer ', '', $headers['Authorization']);
    // $data = verifyJwtToken($token);

    // if (!$data) {
    //     http_response(401, 'Invalid or expired token');
    // }

    // return $data;
}

function generateUUIDv4(): string
{
    // Generate 16 random bytes (128 bits)
    $data = random_bytes(16);

    // Set version to 0100 (UUID v4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

    // Set bits 6-7 of the clock_seq_hi_and_reserved to 10
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    // Format as UUID string
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


