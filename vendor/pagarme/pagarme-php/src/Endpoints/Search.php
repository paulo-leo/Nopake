<?php

namespace PagarMe\Endpoints;

use PagarMe\Client;
use PagarMe\Routes;
use PagarMe\Endpoints\Endpoint;

class Search extends Endpoint
{
    /**
     * @param array|null $payload
     *
     * @return \ArrayObject
     */
    public function get(array $payload = null)
    {
        return $this->client->request(
            self::GET,
            Routes::search()->base(),
            ['query' => $payload]
        );
    }
}
