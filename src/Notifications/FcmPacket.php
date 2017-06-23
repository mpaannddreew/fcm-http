<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-22
 * Time: 12:09 PM
 */

namespace FannyPack\FcmHttp\Notifications;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class FcmPacket implements Arrayable, Jsonable
{
    const HTTP_PIPELINE = 'http';
    const XMPP_PIPELINE = 'xmpp';

    /**
     * recipient registration ids
     *
     * @var array
     */
    public $registration_ids = [];

    /**
     * recipient registration id | topic | group id
     *
     * @var null|string
     */
    public $to;

    /**
     * condition for recipient topics
     *
     * @var null|string
     */
    public $condition;

    /**
     * notification message
     *
     * @var FcmMessage|null
     */
    public $message;

    /**
     * fcm connection server protocol
     *
     * @var string
     */
    public $pipeline = self::HTTP_PIPELINE;

    /**
     * @var string|null
     */
    protected $message_id;

    /**
     * FcmPacket constructor.
     * @param string $pipeline
     * @param string|null $registration_id
     * @param array $registration_ids
     * @param string|null $condition
     * @param FcmMessage|null $message
     */
    public function __construct($pipeline = self::HTTP_PIPELINE, $registration_id = null, $registration_ids = [], $condition = null, FcmMessage $message = null)
    {
        $this->pipeline = $pipeline;
        $this->to = $registration_id;
        $this->registration_ids = $registration_ids;
        $this->condition = $condition;
        $this->message = $message;
    }

    /**
     * set notification message
     *
     * @param FcmMessage $message
     * @return $this
     */
    public function message(FcmMessage $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * set recipient registration id | topic | group id
     *
     * @param $registration_id
     * @return $this
     */
    public function to($registration_id)
    {
        $this->to = $registration_id;

        return $this;
    }

    /**
     * override recipient registration ids
     *
     * @param array $registration_ids
     * @return $this
     */
    public function toMany($registration_ids = [])
    {
        $this->registration_ids = $registration_ids;

        return $this;
    }

    /**
     * add registration_id to recipient array
     *
     * @param $registration_id
     * @return $this
     */
    public function addRecipientId($registration_id)
    {
        $this->registration_ids[] = $registration_id;

        return $this;
    }

    /**
     * set new FCM connection server protocol
     *
     * @param $pipeline
     * @return $this
     */
    public function pipeline($pipeline)
    {
        $this->pipeline = $pipeline;

        return $this->setMessageId();
    }

    /**
     * set recipient topic condition
     *
     * @param $condition
     * @return $this
     */
    public function condition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * generate and return a unique message id for xmpp
     *
     * @return string
     */
    protected function getMessageId()
    {
        mt_srand((double)microtime() * 10000); // optional for php 4.2.0 and up.
        $char_id = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $message_id = substr($char_id, 0, 8) . $hyphen
            . substr($char_id, 8, 4) . $hyphen
            . substr($char_id, 12, 4) . $hyphen
            . substr($char_id, 16, 4) . $hyphen
            . substr($char_id, 20, 12);
        return $message_id;
    }

    /**
     * assign new unique message id
     *
     * @return $this
     */
    protected function setMessageId()
    {
        if($this->pipeline == self::XMPP_PIPELINE)
            $this->message_id = $this->getMessageId();

        return $this;
    }
    
    public function messageId()
    {
        return $message_id = $this->pipeline == self::XMPP_PIPELINE ? $this->message_id : null;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $packet = [];
        if ($this->pipeline == self::XMPP_PIPELINE)
        {
            if ($this->to && $this->condition)
            {
                $packet['condition'] = $this->condition;
            }elseif($this->to)
            {
                $packet['to'] = $this->to;
            }elseif($this->condition)
            {
                $packet['condition'] = $this->condition;
            }

            if($this->message_id)
                $packet['message_id'] = $this->message_id;
        }else
        {
            if ($this->to && $this->registration_ids && $this->condition)
            {
                $packet['registration_ids'] = $this->registration_ids;
            }elseif ($this->to && $this->registration_ids)
            {
                $packet['registration_ids'] = $this->registration_ids;
            }elseif ($this->registration_ids && $this->condition)
            {
                $packet['registration_ids'] = $this->registration_ids;
            }elseif ($this->to && $this->condition)
            {
                $packet['condition'] = $this->condition;
            }elseif($this->to)
            {
                $packet['to'] = $this->to;
            }elseif($this->registration_ids)
            {
                $packet['registration_ids'] = $this->registration_ids;
            }
        }

        if ($this->message)
            $packet = array_merge($packet, $this->message->toArray());
        
        return $packet;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}