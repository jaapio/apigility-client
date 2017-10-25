<?php


namespace PolderKnowledge\ApigilityClient\Validation;


final class Violation
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $message;

    /**
     * Violation constructor.
     * @param string $type
     * @param string $message
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
