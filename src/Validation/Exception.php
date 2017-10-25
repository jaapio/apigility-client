<?php
/**
 * Polder Knowledge / ApigilityClient (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/apigilityclient for the canonical source repository
 * @copyright Copyright (c) 2017 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/apigilityclient/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\ApigilityClient\Validation;

use GuzzleHttp\Exception\BadResponseException;
use Lukasoppermann\Httpstatus\Httpstatuscodes;

final class Exception extends \RuntimeException
{
    /**
     * @var array[string, Violation[]]
     */
    private $fields = [];

    public static function createFromResponseContent(string $content, BadResponseException $previousException) : Exception
    {
        try {
            $contentArray = \GuzzleHttp\json_decode($content, true);
            $exception = new self('Validation failed', Httpstatuscodes::HTTP_UNPROCESSABLE_ENTITY, $previousException);
            if (array_key_exists('validation_messages', $contentArray)) {
                foreach ($contentArray['validation_messages'] as $field => $violation) {
                    foreach ($violation as $type => $message) {
                        $exception->addFieldViolation($field, new Violation($type, $message));
                    }
                }
            }

            return $exception;
        } catch (\InvalidArgumentException $e) {
            return new self('Validation failed without valid json content', Httpstatuscodes::HTTP_UNPROCESSABLE_ENTITY, $e);
        }
    }

    private function addFieldViolation(string $field, Violation $violation)
    {
        $this->fields[$field][] = $violation;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
