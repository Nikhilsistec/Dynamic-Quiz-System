<?php

namespace App\Quiz;

use App\Quiz\Contracts\QuestionTypeHandlerInterface;
use InvalidArgumentException;

class QuestionTypeRegistry
{
    /** @var array<string, QuestionTypeHandlerInterface> */
    private array $handlers = [];

    public function register(QuestionTypeHandlerInterface $handler): void
    {
        $this->handlers[$handler->type()] = $handler;
    }

    public function get(string $type): QuestionTypeHandlerInterface
    {
        if (! isset($this->handlers[$type])) {
            throw new InvalidArgumentException("No handler registered for question type: {$type}");
        }

        return $this->handlers[$type];
    }

    /**
     * Returns [type_key => label] pairs for <select> elements.
     *
     * @return array<string, string>
     */
    public function typeOptions(): array
    {
        $options = [];
        foreach ($this->handlers as $handler) {
            $options[$handler->type()] = $handler->label();
        }
        return $options;
    }

    /** @return QuestionTypeHandlerInterface[] */
    public function all(): array
    {
        return array_values($this->handlers);
    }

    public function has(string $type): bool
    {
        return isset($this->handlers[$type]);
    }
}
