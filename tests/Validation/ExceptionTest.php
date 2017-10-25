<?php

namespace PolderKnowledge\ApigilityClientTest\Validation;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use PolderKnowledge\ApigilityClient\Validation\Violation;
use PolderKnowledge\ApigilityClient\Validation\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testCreateSingleField()
    {
        $exception = Exception::createFromResponseContent(
            '{"validation_messages":{"Test":{"isEmpty":"Value is required and can\'t be empty"}},
            "type":"http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html","title":"Unprocessable Entity",
            "status":422,"detail":"Failed Validation"}',
            new BadResponseException('message', new Request('post', 'url'))
        );

        $this->assertEquals(
            [
                'Test' => [
                    new Violation('isEmpty', 'Value is required and can\'t be empty')
                ]
            ],
            $exception->getFields()
        );
    }

    public function testCreateMultipleField()
    {
        $exception = Exception::createFromResponseContent('{"validation_messages":
        {"Test":{"hostnameInvalidHostname":"The input does not match the expected structure for a DNS hostname",
        "hostnameLocalNameNotAllowed":"The input appears to be a local network name but local network names are not allowed",
        "notIpAddress":"The input does not appear to be a valid IP address"}},
        "type":"http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html",
        "title":"Unprocessable Entity","status":422,"detail":"Failed Validation"}',
            new BadResponseException('message', new Request('post', 'url'))
        );

        $this->assertEquals(
            [
                'Test' => [
                    new Violation('hostnameInvalidHostname', 'The input does not match the expected structure for a DNS hostname'),
                    new Violation('hostnameLocalNameNotAllowed', 'The input appears to be a local network name but local network names are not allowed'),
                    new Violation('notIpAddress', 'The input does not appear to be a valid IP address'),
                ]
            ],
            $exception->getFields()
        );
    }

    public function testCreateFromInvalidJson()
    {
        $exception = Exception::createFromResponseContent(
            'dasdaas',
            new BadResponseException('message', new Request('post', 'url'))
        );

        self::assertEquals([], $exception->getFields());
    }
}
