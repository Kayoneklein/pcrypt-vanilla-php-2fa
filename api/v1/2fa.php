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

    public function activate($payload, $userId)
    {


        if (!isset($payload['payload'])) {
            http_response(400, 'Invalid activation payload');
        }

        /**
         * @var mixed
         * The payload is decrypted using the security service provider.
         * This returns a json encoded string of the device parameters passed from the frontend
         */
        $decrypted = $this->securityService->decrypt($payload['payload']);


        /**
         * @var mixed
         * The json encoded string which was decrypted is decoded or deserialized into the EncryptData object
         * The EncryptData object is in the path: /app/Interfaces/EncryptData.php
         */
        $data = EncryptData::deserialize($decrypted);
        // $data = $payload['payload'];

        /**
         * @var mixed
         * Ensure all the valid parameters for 2fa is in the payload
         */
        $this->validate_2fa_request($data);

        // $payload = [
        //     'public_key' => $data['public_key'],
        //     'server_url' => $data['server_url'],
        //     'device_id' => $data['device_id'],
        //     'device_token' => $data['device_token'],
        //     'is_mobile_device' => $data['is_mobile_device'],
        //     'timestamp' => date('Y-m-d H:i:s'),
        //     'server_id' => generateUUIDv4(),
        // ];

        // $payload = [
        //     'public_key' => $data->public_key,
        //     'server_url' => $data->server_url,
        //     'device_id' => $data->device_id,
        //     'device_token' => $data->device_token,
        //     'is_mobile_device' => $data->is_mobile_device,
        //     'timestamp' => date('Y-m-d H:i:s'),
        //     'server_id' => generateUUIDv4(),
        // ];

        ///Save the data on the two_fa table of the database
        global $pdo;
        $two_fa = new TwoFAModel($pdo, $userId);
        $twoFa = $two_fa->activate($data);

        if ($twoFa) {
            /**
             * @var mixed
             * The updated $data object is json encoded to convert it into a string.
             */
            $data = new EncryptData(
                $twoFa['user_id'],
                $twoFa['server_url'],
                $twoFa['device_id'],
                $twoFa['public_key'],
                $twoFa['server_id'],
                $twoFa['timestamp'],
                $twoFa['device_token'],
                $twoFa['is_mobile_device'],

            );
            $serialize = $data->serialize();

            /**
             * @var mixed
             * The json encoded string is then encrypted before it is sent to the client
             */
            $encrypt = $this->securityService->encrypt($serialize);
            http_response(200, $encrypt);
        }

        http_response(400, 'An error occurred. Try again');
    }
    public function change_device($payload, $userId)
    {
        $decrypted = $this->securityService->decrypt($payload['payload']);
        $data = EncryptData::deserialize($decrypted);

        $this->validate_2fa_request($data);

        global $pdo;
        $two_fa = new TwoFAModel($pdo, $userId);
        $twoFa = $two_fa->change_device($data);

        if ($twoFa) {
            /**
             * @var mixed
             * The updated $data object is json encoded to convert it into a string.
             */
            $data = new EncryptData(
                $twoFa['user_id'],
                $twoFa['server_url'],
                $twoFa['device_id'],
                $twoFa['public_key'],
                $twoFa['server_id'],
                $twoFa['timestamp'],
                $twoFa['device_token'],
                $twoFa['is_mobile_device'],

            );
            $serialize = $data->serialize();

            /**
             * @var mixed
             * The json encoded string is then encrypted before it is sent to the client
             */
            $encrypt = $this->securityService->encrypt($serialize);
            http_response(200, $encrypt);
        }

        http_response(400, 'An error occurred. Try again');
    }
}