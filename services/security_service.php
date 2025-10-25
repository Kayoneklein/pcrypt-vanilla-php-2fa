<?php

class SecurityServiceProvider
{

    /* Encrypt data using OpenSSL */
    public function encrypt(string $message): string
    {
        /**
         * @var mixed
         * A 32 byte key is generated in openssl and stored in an env variable
         * The key is generated using the formula $rawKey = random_bytes(32);
         * After which, the key is encoded using $key = base64_encode($rawKey);
         * it is this encoded string this is stored in .env file as OPEN_SSL_ENCRYPTION_KEY_32
         *
         * The encoded key is now being decoded below for use in the encryption process
         */
        $key = base64_decode(getenv("OPEN_SSL_ENCRYPTION_KEY_32"));

        /**
         * @var mixed
         * There are various cipher in openssl. the one used for this function is the AES-256-CBC cipher. This is static
         */
        $cipher = getenv("OPEN_SSL_CIPHER");

        /**
         * @var mixed
         * We need to generate an Initialization Vector (IV) to be stored on the .env file.
         * This formula was used to generate the IV: $iv = random_bytes(openssl_cipher_iv_length($cipher));
         * The generated IV was then encoded to be stored on the .env file using the formula; $base64IV = base64_encode($iv);
         * The generated encoded string was then stored in the .env file as OPEN_SSL_BASE_64_IV
         *
         * This value was thus obtained below from the .env file
         */
        $base64Iv = getenv("OPEN_SSL_BASE_64_IV");

        //The base64 IV was thus decoded back into its true form to be used in the encryption process.
        $iv = base64_decode($base64Iv);

        /**
         * @var mixed
         * Now the actual encryption can be done with the formula below. The parameters passed to the formula are:
         * 1. $message: This is the actual string to be encrypted by the user.
         * 2. $cipher: This is the preferred cipher algorithm chosen for the encryption process as stated above
         * 3. $key: This is the previously derived openssl 32 byte key saved in the .env file
         * 4. OPENSSL_RAW_DATA: this is a static value. it is an option for encryption. see documentation (https://www.php.net/manual/en/function.openssl-encrypt.php) for further clarification
         * 5. $iv: This is the IV variable generated and stored on the .env file
         */
        $encrypted = openssl_encrypt($message, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        //After encryption, the resulting value is thus encoded into a string.
        $base64Encrypted = base64_encode($encrypted);
        // $base64Iv = base64_encode($iv);

        return $base64Encrypted;
    }
    /* Decrypt data using OpenSSL */
    public function decrypt(string $data): string
    {
        /**
         * @var mixed
         * A 32 byte key is generated in openssl and stored in an env variable
         * The key is generated using the formula $rawKey = random_bytes(32);
         * After which, the key is encoded using $key = base64_encode($rawKey);
         * it is this encoded string this is stored in .env file as OPEN_SSL_ENCRYPTION_KEY_32
         *
         * The encoded key is now being decoded below for use in the encryption process
         */
        $key = base64_decode(getenv("OPEN_SSL_ENCRYPTION_KEY_32"));


        /**
         * @var mixed
         * There are various cipher in openssl. the one used for this function is the AES-256-CBC cipher. This is static
         */
        $cipher = getenv("OPEN_SSL_CIPHER");


        /**
         * @var mixed
         * We need to generate an Initialization Vector (IV) to be stored on the .env file.
         * This formula was used to generate the IV: $iv = random_bytes(openssl_cipher_iv_length($cipher));
         * The generated IV was then encoded to be stored on the .env file using the formula; $base64IV = base64_encode($iv);
         * The generated encoded string was then stored in the .env file as OPEN_SSL_BASE_64_IV
         *
         * This value was thus obtained below from the .env file
         */
        $base64Iv = getenv("OPEN_SSL_BASE_64_IV");
        //The base64 IV was thus decoded back into its true form to be used in the decryption process.
        $iv = base64_decode($base64Iv);

        /**
         * @var mixed
         * Since the decrypted value was encoded into a string, it will need to be decoded into its true form.
         * The function below ensures that this conversion to base64 is done before decryption.
         */
        $encrypted = base64_decode($data);

        /**
         * @var mixed
         * Now the actual decryption can be done with the formula below. The parameters passed to the formula are:
         * 1. $encrypted: This is the decrypted value in its raw form.
         * 2. $cipher: This is the preferred cipher algorithm chosen for the decryption process as stated above
         * 3. $key: This is the previously derived openssl 32 byte key saved in the .env file
         * 4. OPENSSL_RAW_DATA: this is a static value. it is an option for decryption. see documentation (https://www.php.net/manual/en/function.openssl-decrypt.php) for further clarification
         * 5. $iv: This is the IV variable generated and stored on the .env file
         *
         * The returned value after decryption is usually a string
         */
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

}