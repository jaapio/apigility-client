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
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use PolderKnowledge\ApigilityClient\Validation\Exception as ValidationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Client
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function send(RequestInterface $request) : ResponseInterface
    {
        try {
            return $this->client->send($request);
        } catch (BadResponseException $e) {
            switch ($e->getCode()) {
                case Httpstatuscodes::HTTP_UNPROCESSABLE_ENTITY:
                    $content = $e->getResponse()->getBody()->getContents();
                    throw ValidationException::createFromResponseContent($content, $e);
                case Httpstatuscodes::HTTP_FORBIDDEN:
                    throw new ForbiddenException('Forbidden', $e->getCode(), $e);
                default:
                    throw new GenericException($e->getMessage(), $e->getCode(), $e);
            }
        } catch (GuzzleException $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
