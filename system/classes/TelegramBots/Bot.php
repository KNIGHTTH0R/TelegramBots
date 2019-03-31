<?php

namespace TelegramBots;

/**
 * Class Bot
 * @package TelegramBots
 */
class Bot
{
    const PARSE_MODE_HTML = 'HTML';
    const PARSE_MODE_MARKDOWN = 'markdown';

    const ACTION_TYPING = 'typing';
    const ACTION_UPLOADING_PHOTO = 'upload_photo';
    const ACTION_RECORDING_VIDEO = 'record_video';
    const ACTION_UPLOADING_VIDEO = 'upload_video';
    const ACTION_RECORDING_AUDIO = 'record_audio';
    const ACTION_UPLOADING_AUDIO = 'upload_audio';
    const ACTION_UPLOADING_DOCUMENT = 'upload_document';
    const ACTION_FINDING_LOCATION = 'find_location';
    const ACTION_RECORDING_VIDEO_NOTE = 'record_video_note';
    const ACTION_UPLOADING_VIDEO_NOTE = 'upload_video_note';

    const CAN_ADD_WEB_PAGE_PREVIEWS = 'can_add_web_page_previews';
    const CAN_CHANGE_INFO = 'can_change_info';
    const CAN_DELETE_MESSAGES = 'can_delete_messages';
    const CAN_EDIT_MESSAGES = 'can_edit_messages';
    const CAN_INVITE_USERS = 'can_invite_users';
    const CAN_PIN_MESSAGES = 'can_pin_messages';
    const CAN_POST_MESSAGES = 'can_post_messages';
    const CAN_PROMOTE_MEMBERS = 'can_promote_members';
    const CAN_RESTRICT_MEMBERS = 'can_restrict_members';
    const CAN_SEND_MEDIA_MESSAGES = 'can_send_media_messages';
    const CAN_SEND_MESSAGES = 'can_send_messages';
    const CAN_SEND_OTHER_MESSAGES = 'can_send_other_messages';

    /**
     * @var string Bot token
     */
    private $token;

    /**
     * @var string
     */
    private $bot_username;

    /**
     * @var string
     */
    private $default_parse_mode = self::PARSE_MODE_HTML;

    /**
     * Bot constructor.
     * @param string $token
     * @param string $bot_username
     */
    public function __construct(string $token, string $bot_username)
    {
        $this->token = $token;
        $this->bot_username = $bot_username;
    }

    /**
     * @param string $parse_mode
     * @return Bot
     * @throws \Exception
     */
    public function setDefaultParseMode(string $parse_mode): self
    {
        if (!self::checkParseMode($parse_mode)) {
            throw new \Exception("Unknown parse mode: {$parse_mode}");
        }
        $this->default_parse_mode = $parse_mode;
        return $this;
    }

    /**
     * Request to Telegram Bots API method with your bot's token.
     * @param string $method
     * @param array $params
     * @param int $permissions
     * @return Request
     */
    public function request(string $method, array $params = [], int $permissions = 0): Request
    {
        return new Request($this->token, $method, $params, $permissions);
    }

    /**
     * Use this method to receive incoming updates using long polling. An Array of Update objects is returned by this API method.
     * @param int|null $offset
     * @param int|null $limit
     * @return Request
     */
    public function getUpdates(int $offset = null, int $limit = null): Request
    {
        return $this->request('getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    /**
     * A simple method for testing your bot's auth token. API method returns basic information about the bot in form of a User object.
     * @return Request
     */
    public function getMe(): Request
    {
        return $this->request('getMe');
    }

    /**
     * Use this method to specify a url and receive incoming updates via an outgoing webhook. Whenever there is an update for the bot, we will send an HTTPS POST request to the specified url, containing a JSON-serialized Update. In case of an unsuccessful request, we will give up after a reasonable amount of attempts. API method returns True on success.
     * @param string $url
     * @param string|null $certificate
     * @param int|null $max_connections
     * @param array|null $allowed_updates
     * @return Request
     */
    public function setWebhook(string $url, string $certificate = null, int $max_connections = null, array $allowed_updates = null): Request
    {
        return $this->request('setWebhook', [
            'url' => $url,
            'certificate' => $certificate,
            'max_connections' => $max_connections,
            'allowed_updates' => $allowed_updates,
        ]);
    }

    /**
     * Use this method to remove webhook integration if you decide to switch back to getUpdates. This API method returns True on success.
     * @return Request
     */
    public function deleteWebhook(): Request
    {
        return $this->request('deleteWebhook');
    }

    /**
     * Use this method to get current webhook status. Requires no parameters. On success, this API method returns a WebhookInfo object. If the bot is using getUpdates, will return an object with the url field empty.
     * @return Request
     */
    public function getWebhookInfo(): Request
    {
        return $this->request('getWebhookInfo');
    }

    /**
     * Use this method to send text messages. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $text
     * @return Request
     * @throws \Exception
     */
    public function sendMessage(string $chat_id, string $text): Request
    {
        return $this
            ->request(
                'sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
            ],
                Request::CAN_DISABLE_NOTIFICATION |
                Request::CAN_SET_PARSE_MODE |
                Request::CAN_DISABLE_WEB_PAGE_PREVIEW |
                Request::CAN_REPLY_TO_MESSAGE |
                Request::CAN_ADD_REPLY_MARKUP
            )
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to forward messages of any kind. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $from_chat_id
     * @param int $message_id
     * @return Request
     */
    public function forwardMessage(string $chat_id, string $from_chat_id, int $message_id): Request
    {
        return $this->request('forwardMessage', [
            'chat_id' => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id' => $message_id,
        ], Request::CAN_DISABLE_NOTIFICATION);
    }

    /**
     * Use this method to send photos. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $photo
     * @param string|null $caption
     * @return Request
     * @throws \Exception
     */
    public function sendPhoto(string $chat_id, string $photo, string $caption = null): Request
    {
        return $this
            ->request('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo,
                'caption' => $caption,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. On success, the sent Message is returned. Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * @param string $chat_id
     * @param string $audio
     * @param string|null $caption
     * @param string|null $thumb
     * @param int|null $duration
     * @param string|null $title
     * @param string|null $performer
     * @return Request
     * @throws \Exception
     */
    public function sendAudio(string $chat_id, string $audio, string $caption = null, string $thumb = null, int $duration = null, string $title = null, string $performer = null): Request
    {
        return $this
            ->request('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio,
                'caption' => $caption,
                'title' => $title,
                'performer' => $performer,
                'thumb' => $thumb,
                'duration' => $duration,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to send general files. On success, the sent Message is returned by this API method. Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
     * @param string $chat_id
     * @param string $document
     * @param string|null $caption
     * @param string|null $thumb
     * @return Request
     * @throws \Exception
     */
    public function sendDocument(string $chat_id, string $document, string $caption = null, string $thumb = null): Request
    {
        return $this
            ->request('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document,
                'caption' => $caption,
                'thumb' => $thumb,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to send general files. On success, the sent Message is returned by this API method. Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
     * @param string $chat_id
     * @param string $video
     * @param string|null $caption
     * @param string|null $thumb
     * @param int|null $duration
     * @param int|null $width
     * @param int|null $height
     * @param bool|null $supports_streaming
     * @return Request
     * @throws \Exception
     */
    public function sendVideo(string $chat_id, string $video, string $caption = null, string $thumb = null, int $duration = null, int $width = null, int $height = null, bool $supports_streaming = null): Request
    {
        return $this
            ->request('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video,
                'caption' => $caption,
                'thumb' => $thumb,
                'duration' => $duration,
                'width' => $width,
                'height' => $height,
                'supports_streaming' => $supports_streaming,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound). On success, the sent Message is returned by this API method. Bots can currently send animation files of up to 50 MB in size, this limit may be changed in the future.
     * @param string $chat_id
     * @param string $animation
     * @param string|null $caption
     * @param string|null $thumb
     * @param int|null $duration
     * @param int|null $width
     * @param int|null $height
     * @return Request
     * @throws \Exception
     */
    public function sendAnimation(string $chat_id, string $animation, string $caption = null, string $thumb = null, int $duration = null, int $width = null, int $height = null): Request
    {
        return $this
            ->request('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation,
                'caption' => $caption,
                'thumb' => $thumb,
                'duration' => $duration,
                'width' => $width,
                'height' => $height,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document). On success, the sent Message is returned by this API method. Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     * @param string $chat_id
     * @param string $voice
     * @param string|null $caption
     * @param int|null $duration
     * @return Request
     * @throws \Exception
     */
    public function sendVoice(string $chat_id, string $voice, string $caption = null, int $duration = null): Request
    {
        return $this
            ->request('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice,
                'caption' => $caption,
                'duration' => $duration,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * As of v.4.0, Telegram clients support rounded square mp4 videos of up to 1 minute long. Use this method to send video messages. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $video_note
     * @param string|null $caption
     * @param string|null $thumb
     * @param int|null $duration
     * @param int|null $length
     * @return Request
     * @throws \Exception
     */
    public function sendVideoNote(string $chat_id, string $video_note, string $caption = null, string $thumb = null, int $duration = null, int $length = null): Request
    {
        return $this
            ->request('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note,
                'caption' => $caption,
                'thumb' => $thumb,
                'length' => $length,
                'duration' => $duration,
            ], Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP);
    }

    public function sendMediaGroup()
    {
//        Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE
    }

    /**
     * Use this method to send point on the map. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param float $latitude
     * @param float $longitude
     * @param int|null $live_period
     * @return Request
     * @throws \Exception
     */
    public function sendLocation(string $chat_id, float $latitude, float $longitude, int $live_period = null): Request
    {
        return $this->request('sendLocation', [
            'chat_id' => $chat_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'live_period' => $live_period,
        ], Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method to edit live location messages sent by the bot or via the bot (for inline bots). A location can be edited until its live_period expires or editing is explicitly disabled by a call to stopMessageLiveLocation. On success, if the edited message was sent by the bot, the edited Message is returned, otherwise True is returned by this API method.
     * @param string $chat_id
     * @param float $latitude
     * @param float $longitude
     * @param int|null $message_id
     * @param string|null $inline_message_id
     * @return Request
     * @throws \Exception
     */
    public function editMessageLiveLocation(string $chat_id, float $latitude, float $longitude, int $message_id = null, string $inline_message_id = null): Request
    {
        if ($message_id === null && $inline_message_id === null) {
            throw new \Exception('$message_id or $inline_message_id arguments must be not null');
        }

        return $this->request('editMessageLiveLocation', [
            'chat_id' => $chat_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'message_id' => $message_id,
            'inline_message_id' => $inline_message_id,
        ], Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method to stop updating a live location message sent by the bot or via the bot (for inline bots) before live_period expires. On success, if the message was sent by the bot, the sent Message is returned, otherwise True is returned by this API method.
     * @param string $chat_id
     * @param int|null $message_id
     * @param string|null $inline_message_id
     * @return Request
     * @throws \Exception
     */
    public function stopMessageLiveLocation(string $chat_id, int $message_id = null, string $inline_message_id = null): Request
    {
        if ($message_id === null && $inline_message_id === null) {
            throw new \Exception('$message_id or $inline_message_id arguments must be not null');
        }

        return $this->request('stopMessageLiveLocation', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'inline_message_id' => $inline_message_id,
        ], Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method to send information about a venue. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param float $latitude
     * @param float $longitude
     * @param string|null $address
     * @param string|null $title
     * @return Request
     */
    public function sendVenue(string $chat_id, float $latitude, float $longitude, string $address = null, string $title = null): Request
    {
        return $this
            ->request('sendVenue', [
                'chat_id' => $chat_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address,
                'title' => $title,
            ], Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method to send phone contacts. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $phone_number
     * @param string $first_name
     * @param string|null $last_name
     * @return Request
     */
    public function sendContact(string $chat_id, string $phone_number, string $first_name, string $last_name = null): Request
    {
        return $this->request('sendContact', [
            'chat_id' => $chat_id,
            'phone_number' => $phone_number,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ], Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status). This API method returns True on success.
     * @param string $chat_id
     * @param string $action
     * @return Request
     */
    public function sendChatAction(string $chat_id, string $action): Request
    {
        return $this->request('sendChatAction', [
            'chat_id' => $chat_id,
            'action' => $action,
        ]);
    }

    /**
     * Use this method to get a list of profile pictures for a user. This API method returns a UserProfilePhotos object.
     * @param int $user_id
     * @param int|null $limit
     * @param int|null $offset
     * @return Request
     */
    public function getUserProfilePhotos(int $user_id, int $limit = null, int $offset = null): Request
    {
        return $this->request('getUserProfilePhotos', [
            'user_id' => $user_id,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Use this method to get basic info about a file and prepare it for downloading. For the moment, bots can download files of up to 20MB in size. On success, a File object is returned. The file can then be downloaded via the link https://api.telegram.org/file/bot<token>/<file_path>, where <file_path> is taken from the response. It is guaranteed that the link will be valid for at least 1 hour. When the link expires, a new one can be requested by calling getFile again.
     * @param string $file_id
     * @return Request
     */
    public function getFile(string $file_id): Request
    {
        return $this->request('getFile', ['file_id' => $file_id]);
    }

    /**
     * Getting the file url for downloading
     * @param string $file_path
     * @return string
     */
    public function getFileUrl(string $file_path): string
    {
        return "https://api.telegram.org/file/bot{$this->token}/{$file_path}";
    }

    /**
     * Use this method to kick a user from a group, a supergroup or a channel. In the case of supergroups and channels, the user will not be able to return to the group on their own using invite links, etc., unless unbanned first. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success by this API method.
     * @param string $chat_id
     * @param int $user_id
     * @param int|null $until_date
     * @return Request
     */
    public function kickChatMember(string $chat_id, int $user_id, int $until_date = null): Request
    {
        return $this->request('kickChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'until_date' => $until_date,
        ]);
    }

    /**
     * Use this method to unban a previously kicked user in a supergroup or channel. The user will not return to the group or channel automatically, but will be able to join via link, etc. The bot must be an administrator for this to work. This API method returns True on success.
     * @param string $chat_id
     * @param int $user_id
     * @return Request
     */
    public function unbanChatMember(string $chat_id, int $user_id): Request
    {
        return $this->request('unbanChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Use this method to restrict a user in a supergroup. The bot must be an administrator in the supergroup for this to work and must have the appropriate admin rights. Pass True for all boolean parameters to lift restrictions from a user. This API method returns True on success.
     * @param string $chat_id
     * @param int $user_id
     * @param int|null $until_date
     * @param array|null $permissions
     * @return Request
     * @throws \Exception
     */
    public function restrictChatMember(string $chat_id, int $user_id, int $until_date = null, array $permissions = null): Request
    {

        $params = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'until_date' => $until_date,
        ];

        if ($permissions !== null) {
            $available_permissions = [
                self::CAN_SEND_MESSAGES,
                self::CAN_SEND_MEDIA_MESSAGES,
                self::CAN_SEND_OTHER_MESSAGES,
                self::CAN_ADD_WEB_PAGE_PREVIEWS,
            ];
            $params = array_merge($params, $this->getPermissions($permissions, $available_permissions));
        }

        return $this->request('restrictChatMember', $params);
    }

    /**
     * Use this method to promote or demote a user in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Pass False for all boolean parameters to demote a user. This API method returns True on success.
     * @param string $chat_id
     * @param int $user_id
     * @param array|null $permissions
     * @return Request
     * @throws \Exception
     */
    public function promoteChatMember(string $chat_id, int $user_id, array $permissions = null): Request
    {
        $params = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ];

        if ($permissions !== null) {
            $available_permissions = [
                self::CAN_CHANGE_INFO,
                self::CAN_POST_MESSAGES,
                self::CAN_EDIT_MESSAGES,
                self::CAN_DELETE_MESSAGES,
                self::CAN_INVITE_USERS,
                self::CAN_RESTRICT_MEMBERS,
                self::CAN_PIN_MESSAGES,
                self::CAN_PROMOTE_MEMBERS,
            ];
            $params = array_merge($params, $this->getPermissions($permissions, $available_permissions));
        }

        return $this->request('promoteChatMember', $params);
    }

    /**
     * Use this method to generate a new invite link for a chat; any previously generated link is revoked. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. This API method returns the new invite link as String on success.
     * @param string $chat_id
     * @return Request
     */
    public function exportChatInviteLink(string $chat_id): Request
    {
        return $this->request('exportChatInviteLink', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to set a new profile photo for the chat. Photos can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. This API method returns True on success.
     * @param string $chat_id
     * @param string $photo
     * @return Request
     */
    public function setChatPhoto(string $chat_id, string $photo): Request
    {
        return $this->request('setChatPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
        ]);
    }

    /**
     * Use this method to delete a chat photo. Photos can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. This API method returns True on success.
     * @param string $chat_id
     * @return Request
     */
    public function deleteChatPhoto(string $chat_id): Request
    {
        return $this->request('deleteChatPhoto', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to change the title of a chat. Titles can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. This API method returns True on success.
     * @param string $chat_id
     * @param string $title
     * @return Request
     */
    public function setChatTitle(string $chat_id, string $title): Request
    {
        return $this->request('setChatTitle', [
            'chat_id' => $chat_id,
            'title' => $title,
        ]);
    }

    /**
     * Use this method to change the description of a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. This API method returns True on success.
     * @param string $chat_id
     * @param string|null $description
     * @return Request
     */
    public function setChatDescription(string $chat_id, string $description = null): Request
    {
        return $this->request('setChatDescription', [
            'chat_id' => $chat_id,
            'description' => $description,
        ]);
    }

    /**
     * Use this method to pin a message in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the ‘can_pin_messages’ admin right in the supergroup or ‘can_edit_messages’ admin right in the channel. This API method returns True on success.
     * @param string $chat_id
     * @param int $message_id
     * @return Request
     */
    public function pinChatMessage(string $chat_id, int $message_id): Request
    {
        return $this->request('pinChatMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ], Request::CAN_DISABLE_NOTIFICATION);
    }

    /**
     * Use this method to unpin a message in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the ‘can_pin_messages’ admin right in the supergroup or ‘can_edit_messages’ admin right in the channel. This API method returns True on success.
     * @param string $chat_id
     * @return Request
     */
    public function unpinChatMessage(string $chat_id): Request
    {
        return $this->request('unpinChatMessage', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method for your bot to leave a group, supergroup or channel. This API method returns True on success.
     * @param string $chat_id
     * @return Request
     */
    public function leaveChat(string $chat_id): Request
    {
        return $this->request('leaveChat', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). This API method returns a Chat object on success.
     * @param string $chat_id
     * @return Request
     */
    public function getChat(string $chat_id): Request
    {
        return $this->request('getChat', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to get a list of administrators in a chat. On success, returns an Array of ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned by this API method.
     * @param string $chat_id
     * @return Request
     */
    public function getChatAdministrators(string $chat_id): Request
    {
        return $this->request('getChatAdministrators', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to get the number of members in a chat. This API method returns Int on success.
     * @param string $chat_id
     * @return Request
     */
    public function getChatMembersCount(string $chat_id): Request
    {
        return $this->request('getChatMembersCount', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to get information about a member of a chat. This API method returns a ChatMember object on success.
     * @param string $chat_id
     * @param int $user_id
     * @return Request
     */
    public function getChatMember(string $chat_id, int $user_id): Request
    {
        return $this->request('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Use this method to set a new group sticker set for a supergroup. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Use the field can_set_sticker_set optionally returned in getChat requests to check if the bot can use this method. This API method returns True on success.
     * @param string $chat_id
     * @param string $sticker_set_name
     * @return Request
     */
    public function setChatStickerSet(string $chat_id, string $sticker_set_name): Request
    {
        return $this->request('setChatStickerSet', [
            'chat_id' => $chat_id,
            'sticker_set_name' => $sticker_set_name,
        ]);
    }

    /**
     * Use this method to delete a group sticker set from a supergroup. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Use the field can_set_sticker_set optionally returned in getChat requests to check if the bot can use this method. This API method returns True on success.
     * @param string $chat_id
     * @return Request
     */
    public function deleteChatStickerSet(string $chat_id): Request
    {
        return $this->request('deleteChatStickerSet', ['chat_id' => $chat_id]);
    }

    /**
     * Use this method to send answers to callback queries sent from inline keyboards. The answer will be displayed to the user as a notification at the top of the chat screen or as an alert. On success, True is returned by this API method.
     * @param string $callback_query_id
     * @param string|null $text
     * @param bool|null $show_alert
     * @param string|null $url
     * @param int|null $cache_time
     * @return Request
     */
    public function answerCallbackQuery(string $callback_query_id, string $text = null, bool $show_alert = null, string $url = null, int $cache_time = null): Request
    {
        return $this->request('answerCallbackQuery', [
            'callback_query_id' => $callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert,
            'url' => $url,
            'cache_time' => $cache_time,
        ]);
    }

    /**
     * Use this method to edit text and game messages sent by the bot or via the bot (for inline bots). On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned by this API method.
     * @param string $chat_id
     * @param string $text
     * @param int|null $message_id
     * @param string|null $inline_message_id
     * @return Request
     * @throws \Exception
     */
    public function editMessageText(string $chat_id, string $text, int $message_id = null, string $inline_message_id = null): Request
    {
        if ($message_id === null && $inline_message_id === null) {
            throw new \Exception('$message_id or $inline_message_id arguments must be not null');
        }

        return $this
            ->request('editMessageText', [
                'chat_id' => $chat_id,
                'text' => $text,
                'message_id' => $message_id,
                'inline_message_id' => $inline_message_id,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    /**
     * Use this method to edit captions of messages sent by the bot or via the bot (for inline bots). On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned by this API method.
     * @param string $chat_id
     * @param string $caption
     * @param int|null $message_id
     * @param string|null $inline_message_id
     * @return Request
     * @throws \Exception
     */
    public function editMessageCaption(string $chat_id, string $caption, int $message_id = null, string $inline_message_id = null): Request
    {
        if ($message_id === null && $inline_message_id === null) {
            throw new \Exception('$message_id or $inline_message_id arguments must be not null');
        }

        return $this
            ->request('editMessageCaption', [
                'chat_id' => $chat_id,
                'caption' => $caption,
                'message_id' => $message_id,
                'inline_message_id' => $inline_message_id,
            ], Request::CAN_SET_PARSE_MODE | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP)
            ->setParseMode($this->default_parse_mode);
    }

    public function editMessageMedia()
    {
        //Request::CAN_ADD_REPLY_MARKUP
    }

    public function editMessageReplyMarkup()
    {
        // Request::CAN_ADD_REPLY_MARKUP
    }

    /**
     * Use this method to delete a message, including service messages. This API method returns True on success.
     * @param string $chat_id
     * @param int $message_id
     * @return Request
     */
    public function deleteMessage(string $chat_id, int $message_id): Request
    {
        return $this->request('deleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);
    }

    /**
     * Use this method to send .webp stickers.
     * @param string $chat_id
     * @param string $sticker
     * @return Request
     */
    public function sendSticker(string $chat_id, string $sticker): Request
    {
        return $this
            ->request('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker,
            ], Request::CAN_DISABLE_NOTIFICATION | Request::CAN_REPLY_TO_MESSAGE | Request::CAN_ADD_REPLY_MARKUP);
    }

    /**
     * Use this method to get a sticker set. On success, a StickerSet object is returned by this API method.
     * @param string $name
     * @return Request
     */
    public function getStickerSet(string $name): Request
    {
        return $this->request('getStickerSet', ['name' => $name]);
    }

    /**
     * Use this method to upload a .png file with a sticker for later use in createNewStickerSet and addStickerToSet methods (can be used multiple times). This API method returns the uploaded File on success.
     * @param int $user_id
     * @param string $png_sticker
     * @return Request
     */
    public function uploadStickerFile(int $user_id, string $png_sticker): Request
    {
        return $this->request('uploadStickerFile', [
            'user_id' => $user_id,
            'png_sticker' => $png_sticker,
        ]);
    }

    public function createNewStickerSet(int $user_id, string $name, string $title, string $png_sticker, string $emojis, bool $contains_masks = null, Types\MaskPosition $mask_position = null): Request
    {
        return $this->request('uploadStickerFile', [
            'user_id' => $user_id,
            'name' => $name,
            'title' => $title,
            'png_sticker' => $png_sticker,
            'emojis' => $emojis,
            'contains_masks' => $contains_masks,
            'mask_position' => !empty($mask_position) ? $mask_position->getRequestArray() : null,
        ]);
    }

    /**
     * Use this method to send a game. On success, the sent Message is returned by this API method.
     * @param string $chat_id
     * @param string $game_short_name
     * @return Request
     */
    public function sendGame(string $chat_id, string $game_short_name): Request
    {
        return $this
            ->request('game_short_name', [
                'chat_id' => $chat_id,
                'game_short_name' => $game_short_name,
            ])
            ->enableNotificationModeEdit();
    }

    public function sendInvoice(): Request
    {

//        ->enableNotificationModeEdit()
    }

    /**
     * Getting bot ID from token
     * @return int
     */
    public function getId(): int
    {
        return (int)substr($this->token, 0, strpos($this->token, ':'));
    }

    /**
     * Getting bot token
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Getting bot username
     * @return string
     */
    public function getUsername(): string
    {
        return $this->bot_username;
    }

    /**
     * Strings filter
     * @param string $text
     * @return string
     */
    public static function filterString(string $text): string
    {
        return str_replace(
            ['<', '>', '&', '"'],
            ['&lt;', '&gt;', '&amp;', '&quot;'],
            $text
        );
    }

    /**
     * @param string $parse_mode
     * @return bool
     */
    public static function checkParseMode(string $parse_mode): bool
    {
        return $parse_mode === self::PARSE_MODE_HTML || $parse_mode === self::PARSE_MODE_MARKDOWN;
    }

    private function getPermissions(array $permissions, array $available_permissions): array
    {
        $result = [];
        if ($permissions !== null) {
            // if array is assoc
            if ($permissions !== array_values($permissions)) {
                foreach ($permissions as $permission_name => $permission_access) {
                    if (!in_array($permission_name, $available_permissions)) {
                        throw new \Exception("Permission \"{$permission_name}\" are not supported by this method");
                    }
                    if (!is_bool($permission_access)) {
                        throw new \Exception("Permission value \"{$permission_access}\" are not boolean");
                    }
                    $result[$permission_name] = $permission_access;
                }
            } else {
                foreach ($permissions as $permission_name) {
                    if (!in_array($permission_name, $available_permissions)) {
                        throw new \Exception("Permission \"{$permission_name}\" are not supported by this method");
                    }
                    $result[$permission_name] = true;
                }
            }
        }

        return $result;
    }
}