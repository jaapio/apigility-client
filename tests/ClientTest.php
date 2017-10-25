<?php
/**
 * Polder Knowledge / ApigilityClient (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/apigilityclient for the canonical source repository
 * @copyright Copyright (c) 2017 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/apigilityclient/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\ApigilityClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PolderKnowledge\ApigilityClient\Validation\Exception;

class ClientTest extends TestCase
{
    /**
     * Test that a validation error is formatted properly
     */
    public function testValidationExceptionFormated()
    {
        $request = new Request('get', 'message');

        $httpClient = static::createMock(ClientInterface::class);
        $httpClient->expects(static::once())
            ->method('send')
            ->willThrowException(
                new BadResponseException(
                    'Validation Failed',
                    new Request('post', '/url'),
                    new Response(422, [], '{"validation_messages":{"Test":{"isEmpty":"Value is required and can\'t be empty"}}}')
                ));

        try {
            $client = new Client($httpClient);
            $client->send($request);
        } catch (\Exception $e) {
            static::assertInstanceOf(Exception::class, $e);
            static::assertCount(1, $e->getFields());
            return;
        }

        static::fail('Did\'t catch any expected exception');
    }

    /**
     * @expectedException \PolderKnowledge\ApigilityClient\GenericException
     */
    public function testUnknownErrorCodeHandling()
    {
        $request = new Request('get', 'message');

        $httpClient = static::createMock(ClientInterface::class);
        $httpClient->expects(static::once())
            ->method('send')
            ->willThrowException(
                new BadResponseException(
                    'Message',
                    new Request('post', '/url'),
                    new Response(418, [], 'some body')
                ));

        $client = new Client($httpClient);
        $client->send($request);
    }

    /**
     * @expectedException \PolderKnowledge\ApigilityClient\GenericException
     */
    public function testUnknownGuzzleException()
    {
        $request = new Request('get', 'message');

        $httpClient = static::createMock(ClientInterface::class);
        $httpClient->expects(static::once())
            ->method('send')
            ->willThrowException(
                new class extends \RuntimeException implements GuzzleException {}
            );

        $client = new Client($httpClient);
        $client->send($request);
    }
}
