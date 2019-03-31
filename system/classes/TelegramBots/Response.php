<?php

namespace TelegramBots;

/**
 * Class Response
 * @package TelegramBots
 * @author Maxim Kuvardin <kuvard.in@mail.ru>
 */
class Response
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var array|null
     */
    private $info;

    /**
     * @var array
     */
    private $result;

    /**
     * Response constructor.
     * @param string $method
     * @param array $result
     * @param array|null $info
     */
    public function __construct(string $method, array $result, array $info = null)
    {
        $this->method = $method;
        $this->info = $info;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getInfo(string $key = null)
    {
        return $key !== null ? $this->info[$key] : $this->info;
    }
}