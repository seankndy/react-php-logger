<?php
namespace SeanKndy\ReactLogger;

use React\Stream\WritableStreamInterface;
use React\EventLoop\LoopInterface;
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
    /**
     * @var WritableStreamInterface
     */
    private $stream;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $minLevel;

    public function __construct(string $name, WritableStreamInterface $stream, $minLevel = LogLevel::DEBUG)
    {
        $this->name = $name;
        $this->stream = $stream;
        $this->minLevel = \is_string($minLevel) ? self::LOG_LEVELS[$minLevel] : $minLevel;
    }

    /**
     * Set the minimum log level
     *
     * @var mixed $minLevel
     *
     * @return self
     */
    public function setMinLevel($minLevel)
    {
        $this->minLevel = $minLevel;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = []): void
    {
        if (!isset(self::LOG_LEVELS[$level]) || $this->minLevel > self::LOG_LEVELS[$level]) {
            return;
        }
        $message = '[' . \date('Y-m-d h:m:s') . '] ' . $this->name . '.' .
            \strtoupper($level) . ': ' . (string)$message;
        $message = processPlaceHolders($message, $context);
        $this->stream->write($message);
    }
}
