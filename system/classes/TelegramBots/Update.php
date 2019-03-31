<?php

namespace TelegramBots;

class Update
{
    const ACT_MESSAGE = 'message';
    const ACT_EDITED_MESSAGE = 'edited_message';
    const ACT_CHANNEL_POST = 'channel_post';
    const ACT_EDITED_CHANNEL_POST = 'edited_channel_post';
    const ACT_INLINE_QUERY = 'inline_query';
    const ACT_CHOSEN_INLINE_RESULT = 'chosen_inline_result';
    const ACT_CALLBACK_QUERY = 'callback_query';
    const ACT_SHIPING_QUERY = 'shipping_query';
    const ACT_PRE_CHECKOUT_QUERY = 'pre_checkout_query';

    private $bot;

    private $id;

    private $action;

    private $item_data;

    private $item;

    public function __construct(Bot $bot, array $update_data)
    {
        $this->bot = $bot;
        $this->id = $update_data['id'];

        switch (true) {
            case !empty($update_data[self::ACT_MESSAGE]):
                $this->action = self::ACT_MESSAGE;
                $this->item_data = $update_data[self::ACT_MESSAGE];
                $this->item = new Message($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_EDITED_MESSAGE]):
                $this->action = self::ACT_EDITED_MESSAGE;
                $this->item_data = $update_data[self::ACT_EDITED_MESSAGE];
                $this->item = new Message($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_CHANNEL_POST]):
                $this->action = self::ACT_CHANNEL_POST;
                $this->item_data = $update_data[self::ACT_CHANNEL_POST];
                $this->item = new Message($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_EDITED_CHANNEL_POST]):
                $this->action = self::ACT_EDITED_CHANNEL_POST;
                $this->item_data = $update_data[self::ACT_EDITED_CHANNEL_POST];
                $this->item = new Message($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_INLINE_QUERY]):
                $this->action = self::ACT_INLINE_QUERY;
                $this->item_data = $update_data[self::ACT_INLINE_QUERY];
                $this->item = new InlineQuery($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_CHOSEN_INLINE_RESULT]):
                $this->action = self::ACT_CHOSEN_INLINE_RESULT;
                $this->item_data = $update_data[self::ACT_CHOSEN_INLINE_RESULT];
                $this->item = new ChosenInlineResult($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_CALLBACK_QUERY]):
                $this->action = self::ACT_CALLBACK_QUERY;
                $this->item_data = $update_data[self::ACT_CALLBACK_QUERY];
                $this->item = new CallbackQuery($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_SHIPING_QUERY]):
                $this->action = self::ACT_SHIPING_QUERY;
                $this->item_data = $update_data[self::ACT_SHIPING_QUERY];
                $this->item = new ShippingQuery($bot, $this->item_data);
                break;

            case !empty($update_data[self::ACT_PRE_CHECKOUT_QUERY]):
                $this->action = self::ACT_PRE_CHECKOUT_QUERY;
                $this->item_data = $update_data[self::ACT_PRE_CHECKOUT_QUERY];
                $this->item = new PreCheckoutQuery($bot, $this->item_data);
                break;

            default:
                $update_data_keys = implode(', ', array_keys($update_data));
                throw new \Exception("Update action not found in keys: $update_data_keys");
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getItem()
    {
        return $this->item;
    }
}