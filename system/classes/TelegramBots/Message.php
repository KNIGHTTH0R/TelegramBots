<?php

namespace TelegramBots;

class Message
{
    private $bot;

    private $from;

    private $forward_from;

    private $chat;

    private $forward_from_chat;

    private $message_data;

    private $reply_to_message;

    public function __construct(Bot $bot, array $message_data)
    {
        $this->bot = $bot;
        $this->message_data = $message_data;
    }

    public function getId()
    {
        return $this->message_data['message_id'];
    }

    public function getUserFrom()
    {
        if (empty($this->message_data['from'])) {
            return null;
        }

        if (empty($this->from)) {
            $this->from = new User($this->bot, $this->message_data['from']);
        }
        return $this->from;
    }

    public function getDate(string $format = null)
    {
        if (empty($this->message_data['date'])) {
            return null;
        }

        return !empty($format) ? date($format, $this->message_data['date']) : intval($this->message_data['date']);
    }

    public function getChat()
    {
        if (empty($this->message_data['chat'])) {
            return null;
        }

        if (empty($this->chat)) {
            $this->chat = new Chat($this->bot, $this->message_data['chat']);
        }

        return $this->chat;
    }

    public function getUserForwardFrom()
    {
        if (empty($this->message_data['forward_from'])) {
            return null;
        }

        if (empty($this->forward_from)) {
            $this->forward_from = new User($this->bot, $this->message_data['forward_from']);
        }
        return $this->forward_from;
    }

    public function getChatForwardFrom()
    {
        if (empty($this->message_data['forward_from_chat'])) {
            return null;
        }

        if (empty($this->forward_from_chat)) {
            $this->forward_from_chat = new Chat($this->bot, $this->message_data['forward_from_chat']);
        }

        return $this->forward_from_chat;
    }

    public function getMessageIdForwardFrom()
    {
        return !empty($this->message_data['forward_from_message_id']) ? intval($this->message_data['forward_from_message_id']) : null;
    }

    public function getForwardSignature()
    {
        return !empty($this->message_data['forward_signature']) ? $this->message_data['forward_signature'] : null;
    }

    public function getForwardDate(string $format = null)
    {
        if (empty($this->message_data['forward_date'])) {
            return null;
        }

        return !empty($format) ? date($format, $this->message_data['forward_date']) : intval($this->message_data['forward_date']);
    }

    public function getMessageReptyTo()
    {
        if (empty($this->message_data['reply_to_message'])) {
            return null;
        }

        if (empty($this->reply_to_message)) {
            $this->reply_to_message = new Message($this->bot, $this->message_data['reply_to_message']);
        }

        return $this->reply_to_message;
    }

    public function getEditDate(string $format = null)
    {
        if (empty($this->message_data['edit_date'])) {
            return null;
        }

        return !empty($format) ? date($format, $this->message_data['edit_date']) : intval($this->message_data['edit_date']);
    }
}