<?php

namespace WHMCS\Module\Registrar\MKhostRegistrarModule;

final class ApiClientCredentials
{
    private $id;
    private $secret;

    public function __construct(string $id, string $secret)
    {
        $this->id = $id;
        $this->secret = $secret;
    }

    public function id()
    {
        return $this->id;
    }

    public function secret()
    {
        return $this->secret;
    }
}
