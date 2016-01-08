<?php

namespace Opifer\CmsBundle\Logging;

class RedisClient extends \Predis\Client
{
    public function __construct($host, $password, $port)
    {
        parent::__construct([
            'scheme' => 'tcp',
            'host' => $host,
            'password' => $password,
            'port' => $port,
        ]);
    }
}
