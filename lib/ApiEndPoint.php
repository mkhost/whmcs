<?php

namespace WHMCS\Module\Registrar\MKhostRegistrarModule;

final class ApiEndPoint
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function url()
    {
        return $this->url;
    }
}
