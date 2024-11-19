<?php
namespace SeanKndy\ReactLogger;

use React\Stream\WritableStreamInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use function WyriHaximus\PSR3\processPlaceHolders;

final class Logger extends AbstractLogger
{
    /**
     * Log levels and their corresponding integers
     * @const array
     */
    const LOG_LEVELS = [
        LogLevel::DEBUG     => 100,
        LogLevel::INFO      => 200,
        LogLevel::NOTICE    => 250,
        LogLevel::WARNING   => 300,
        LogLevel::ERROR     => 400,
        LogLevel::CRITICAL  => 500,
        LogLevel::ALERT     => 550,
        LogLevel::EMERGENCY => 600
    ];

    private int $minLevel;

    public function __construct(
        private readonly string                  $name,
        private readonly WritableStreamInterface $stream,
        int|string                               $minLevel = LogLevel::DEBUG
    ) {
        $this->setMinLevel($minLevel);
    }

    /**
     * Set the minimum log level
     */
    public function setMinLevel(int|string $minLevel): self
    {
        $this->minLevel = \is_string($minLevel) ? self::LOG_LEVELS[$minLevel] : $minLevel;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!isset(self::LOG_LEVELS[$level]) || $this->minLevel > self::LOG_LEVELS[$level]) {
            return;
        }

        $message = '[' . \date('Y-m-d H:i:s') . '] ' . $this->name . '.' .
            \strtoupper($level) . ': ' . (string)$message;
        $message = processPlaceHolders($message, $context);

        $this->stream->write($message.\PHP_EOL);
    }
}
