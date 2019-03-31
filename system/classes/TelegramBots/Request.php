<?php

namespace TelegramBots;

/**
 * @author Maxim Kuvardin <kuvard.in@mail.ru>
 */
class Request
{
    const REQUEST_TIMEOUT = 30;
    const CONNECT_TIMEOUT = 30;

    const CAN_DISABLE_NOTIFICATION = 1;
    const CAN_REPLY_TO_MESSAGE = 2;
    const CAN_ADD_REPLY_MARKUP = 4;
    const CAN_DISABLE_WEB_PAGE_PREVIEW = 8;
    const CAN_SET_PARSE_MODE = 16;

    
    /**
     * @var string $token Token of the Telegram-bot
     */
    private $token;

    /**
     * @var string $method Required Telegram Bots API method
     */
    private $method;

    /**
     * @var array|null $params Parameters of the request
     */
    public $params;

    /**
     * @var array
     */
    private $permissions;

    /**
     * Constructor of the class
     * @param string $token Token of the Telegram-bot
     * @param string $method Required Telegram Bots API method
     * @param array|array $params Parameters of the request
     */
    public function __construct(string $token, string $method, array $params = [], int $permissions = 0)
    {
        $this->token = $token;
        $this->method = $method;
        $this->params = $params;
        $permissions_bits = array_reverse(str_split(decbin($permissions)));
        $this->permissions = [
            self::CAN_DISABLE_NOTIFICATION => !empty($permissions_bits[0]),
            self::CAN_REPLY_TO_MESSAGE => !empty($permissions_bits[1]),
            self::CAN_ADD_REPLY_MARKUP => !empty($permissions_bits[2]),
            self::CAN_DISABLE_WEB_PAGE_PREVIEW => !empty($permissions_bits[3]),
            self::CAN_SET_PARSE_MODE => !empty($permissions_bits[4]),
        ];
    }

    public function getPermission(int $permission): bool
    {
        return $this->permissions[$permission];
    }

    private static function getPermissionName(int $permission): string
    {
        switch ($permission) {
            case self::CAN_DISABLE_NOTIFICATION:
                return 'CAN_DISABLE_NOTIFICATION';
            case self::CAN_REPLY_TO_MESSAGE:
                return 'CAN_REPLY_TO_MESSAGE';
            case self::CAN_ADD_REPLY_MARKUP:
                return 'CAN_ADD_REPLY_MARKUP';
            case self::CAN_DISABLE_WEB_PAGE_PREVIEW:
                return 'CAN_DISABLE_WEB_PAGE_PREVIEW';
            case self::CAN_SET_PARSE_MODE:
                return 'CAN_SET_PARSE_MODE';
        }
        return 'UNKNOWN_PERMISSION';
    }

    /**
     * @param int $permission
     * @param string $method
     * @return bool
     * @throws \Exception
     */
    private function checkPermission(int $permission, string $method): bool
    {
        if (!$this->getPermission($permission)) {
            $permission_name = self::getPermissionName($permission);
            throw new \Exception("Method {$method} require permission {$permission_name}");
        }
        return true;
    }

    /**
     * @param string $parse_mode
     * @return Request
     * @throws \Exception
     */
    public function setParseMode(string $parse_mode): self
    {
        if ($parse_mode !== Bot::PARSE_MODE_HTML && $parse_mode !== Bot::PARSE_MODE_MARKDOWN) {
            throw new \Exception("Unknown parse mode: {$parse_mode}");
        }
        $this->checkPermission(self::CAN_SET_PARSE_MODE, __METHOD__);
        $this->params['parse_mode'] = $parse_mode;
        return $this;
    }

    /**
     * @return Request
     * @throws \Exception
     */
    public function setNotificationSending(bool $enable): self
    {
        $this->checkPermission(self::CAN_DISABLE_NOTIFICATION, __METHOD__);
        $this->params['disable_notification'] = !$enable;
        return $this;
    }

    /**
     * @param bool $enable
     * @return Request
     * @throws \Exception
     */
    public function setWebPagePreviewSending(bool $enable): self
    {
        $this->checkPermission(self::CAN_DISABLE_WEB_PAGE_PREVIEW, __METHOD__);
        $this->params['disable_web_page_preview'] = !$enable;
        return $this;
    }

    /**
     * @param int $message_id
     * @return Request
     * @throws \Exception
     */
    public function replyToMessageWithId(int $message_id): self
    {
        $this->checkPermission(self::CAN_REPLY_TO_MESSAGE, __METHOD__);
        $this->params['reply_to_message_id'] = $message_id;
        return $this;
    }

    /**
     * @param bool $selective
     * @return Request
     * @throws \Exception
     */
    public function addForceReplyButton(bool $selective = false): self
    {

        $this->checkPermission(self::CAN_ADD_REPLY_MARKUP, __METHOD__);
        $this->params['reply_markup'] = [
            'force_reply' => true,
            'selective' => $selective,
        ];
        return $this;
    }

    /**
     * Get array of the request
     * @return array
     * @throws \Exception
     */
    public function getRequest(): array
    {
        $params = self::processingParams($this->params);
        $params['method'] = $this->method;
        return $params;
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private static function processingParams(array $params): array
    {
        $result = [];
        foreach ($params as $param_key => $param_value) {
            if ($param_value === null) {
                continue;
            }

            if (is_object($param_value)) {
                $class_implements = class_implements($param_value);
                if (!$class_implements || !in_array('TypeInterface', $class_implements)) {
                    $param_class = get_class($param_value);
                    throw new \Exception("Class {$param_class} must implement TypeInterface");
                }
                $result[$param_key] = $param_value->getRequestArray();
            } elseif (is_array($param_value)) {
                $result[$param_key] = self::processingParams($param_value);
            } else {
                $result[$param_key] = $param_value;
            }
        }
        return $result;
    }

    /**
     * @return Response
     * @throws ApiException
     * @throws CurlException
     * @throws \Exception
     */
    public function sendRequest(): Response
    {
        $url = "https://api.telegram.org/bot{$this->token}/{$this->method}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_POSTFIELDS => json_encode($this->getRequest()),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $result = curl_exec($ch);
        $result_decoded = json_decode($result, true);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            $error_code = curl_error($ch);
            $error_description = curl_errno($ch);
            curl_close($ch);
            throw new Exceptions\CurlException($error_code, $error_description);
        }

        curl_close($ch);

        if (empty($result) || $result_decoded === null) {
            throw new Exceptions\ApiException("Empty or non-JSON result: \"{$result}\"", 520);
        }

        $result = $result_decoded;
        if (!empty($result['ok']) && $result['ok'] === false) {
            throw new Exceptions\ApiException($result['description'], $result['error_code']);
        }

        if ($info['http_code'] !== 200) {
            throw new Exceptions\CurlException("HTTP error #{$info['http_code']}", $info['http_code']);
        }

        return new Response($this->method, $result, $info);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString(): string
    {
        return (string)json_encode($this->getRequest());
    }
}