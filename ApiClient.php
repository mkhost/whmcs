<?php

namespace WHMCS\Module\Registrar\MKhostRegistrarModule;

class ApiClient
{
    protected $results = [];
    protected $authToken;

    private $endPoint;
    private $credentials;

    public function __construct(
        ApiClientCredentials $credentials,
        ApiEndPoint $endPoint
    ) {
        $this->credentials = $credentials;
        $this->endPoint = $endPoint;
    }

    public function post($action, $postFields)
    {
        $this->getAuthenticationToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endPoint->url() . $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-MkAuth-Access-Token: ' . $this->authToken
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        logModuleCall(
            'MKhostRegistrarModule',
            $action,
            $postFields,
            $response,
            $this->results
        );

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function put($action, $postFields)
    {
        $this->getAuthenticationToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endPoint->url() . $action);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-MkAuth-Access-Token: ' . $this->authToken
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);
        $this->results = $this->processResponse($response);

        logModuleCall(
            'MKhostRegistrarModule',
            $action,
            $postFields,
            $response,
            $this->results
        );

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function get($action)
    {
        $this->getAuthenticationToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endPoint->url() . $action);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-MkAuth-Access-Token: ' . $this->authToken
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);
        $this->results = $this->processResponse($response);

        logModuleCall(
            'MKhostRegistrarModule',
            $action,
            [],
            $response,
            $this->results
        );

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    private function getAuthenticationToken()
    {
        $ch = curl_init();
        $action = '/v1/app/login';

        curl_setopt($ch, CURLOPT_URL, $this->endPoint->url() . $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'client_id' => $this->credentials->id(),
            'client_secret' => $this->credentials->secret()
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }

        curl_close($ch);
        $results = $this->processResponse($response);

        logModuleCall(
            'MKhostRegistrarModule',
            $action,
            [],
            $response,
            $this->results
        );

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        $this->authToken = $results['data']['token'];
    }

    /**
     * Process API response.
     *
     * @param string $response
     *
     * @return array
     */
    public function processResponse($response)
    {
        $response = json_decode($response, true);

        if (!isset($response['error']) || $response['error']) {
            throw new \Exception('MKhost Registrar Error: ' . $response['data']['messages'][0] ?? 'Unknown Reason');
        }

        return $response;
    }

    /**
     * Get from response results.
     *
     * @param string $key
     *
     * @return string
     */
    public function getFromResponse($key)
    {
        return isset($this->results[$key]) ? $this->results[$key] : '';
    }

    public function getResponse()
    {
        return $this->results;
    }
}
