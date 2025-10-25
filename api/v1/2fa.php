<?php

require_once CORE_PATH . DS . 'app.php';

class TwoFaController
{
    private $securityService;
    /**
     * The SecurityServiceProvider is a special service class that handles the encryption and decryption of
     * data using the open SSL Library.
     */
    public function __construct()
    {
        $this->securityService = new SecurityServiceProvider();
    }

    private function validate_2fa_request($data)
    {
        if (
            !isset($data['public_key']) ||
            !isset($data['server_url']) ||
            !isset($data['device_id']) ||
            !isset($data['device_token']) ||
            !isset($data['is_mobile_device'])
        ) {
            http_response(400, 'Missing some required data to activate two factor auth');
        }
        return true;
    }

    public function activate(User $user, $payload)
    {
        ///Users that already have deviceId on the database has already activated 2FA. So they do not get to do activate it again
        if ($user->device_id !== null) {
            http_response(400, '2FA is active on this account');
        }

        if (!isset($payload['payload'])) {
            http_response(400, 'Invalid activation payload');
        }

        /**
         * @var mixed
         * The payload is decrypted using the security service provider.
         * This returns a json encoded string of the device parameters passed from the frontend
         */

        // $decrypted = $this->securityService->decrypt($payload['payload']);


        /**
         * @var mixed
         * The json encoded string which was decrypted is decoded or deserialized into the EncryptData object
         * The EncryptData object is in the path: /app/Interfaces/EncryptData.php
         */

        // $data = EncryptData::deserialize($decrypted);

        /**
         * @var mixed
         * Further mapping of the $payload is done to include the server generated values
         */
        $data = $payload['payload'];

        $this->validate_2fa_request($data);
        $payload = [
            'public_key' => $data['public_key'],
            'server_url' => $data['server_url'],
            'device_id' => $data['device_id'],
            'device_token' => $data['device_token'],
            'is_mobile_device' => $data['is_mobile_device'],
            'timestamp' => date('Y-m-d H:i:s'),
            'server_id' => generateUUIDv4(),
        ];

        // $payload = [
        //     'public_key' => $data->public_key,
        //     'server_url' => $data->server_url,
        //     'device_id' => $data->device_id,
        //     'device_token' => $data->device_token,
        //     'is_mobile_device' => $data->is_mobile_device,
        //     'timestamp' => date('Y-m-d H:i:s'),
        //     'server_id' => generateUUIDv4(),
        // ];

        ///The user collection on the database is thus updated with the new values
        $updated = $user->update($payload);

        if ($updated && $updated['status']) {
            // $privateKeyEncoded = getenv("PRIVATE_SIGNATURE_KEY_OPEN_SSL");
            // $privateKeyString = base64_decode($privateKeyEncoded);
            // $privateKeyResource = openssl_pkey_get_private($privateKeyString);

            // openssl_sign($serialize, $signature, $privateKeyResource);


            // $data->server_id = $payload['server_id'];
            // $data->timestamp = $payload['timestamp'];
            // $data->id = $user->id;

            $data['server_id'] = $payload['server_id'];
            $data['timestamp'] = $payload['timestamp'];
            $data['id'] = $user->id;

            /**
             * @var mixed
             * The updated $data object is json encoded to convert it into a string.
             */
            // $serialize = $data->serialize();

            /**
             * @var mixed
             * The json encoded string is then encrypted before it is sent to the client
             */
            // $encrypt = $this->securityService->encrypt($serialize);

            http_response(200, $data);
            // http_response(200, $encrypt);

        }

        http_response(400, 'An error occurred. Try again');
    }
    public static function change_device(User $user)
    {
    }
}