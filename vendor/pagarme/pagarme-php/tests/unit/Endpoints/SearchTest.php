<?php

namespace PagarMe\Endpoints\Test;

use PagarMe\Client;
use PagarMe\Endpoints\Search;
use PagarMe\Test\Endpoints\PagarMeTestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

final class SearchTest extends PagarMeTestCase
{
    public function searchProvider()
    {
        return [[[
            'list' => new MockHandler([
                new Response(200, [], self::jsonMock('SearchListMock')),
                new Response(200, [], '[]'),
            ]),
        ]]];
    }

    /**
     * @dataProvider SearchProvider
     */
    public function testSearchGet($mock)
    {
        $requestsContainer = [];

        $client = self::buildClient($requestsContainer, $mock['list']);

        $response = $client->search()->get([
            "type" => "transaction",
            "query" => [
                "query" => [
                    "terms" => [
                        "items.id" => [9]
                    ]
                ]
            ]
        ]);

        $query = self::getQueryString($requestsContainer[0]);

        $this->assertEquals(
            Search::GET,
            self::getRequestMethod($requestsContainer[0])
        );
        $this->assertContains('type=transaction', $query);
        $this->assertEquals(
            json_decode(self::jsonMock('SearchListMock')),
            $response
        );
    }
}
