<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 9:17 PM
 */

namespace FannyPack\FcmHttp\Notifications;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class FcmMessage implements Arrayable, Jsonable
{
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';

    /**
     * The title of the notification.
     *
     * @var string
     */
    public $title;

    /**
     * The message of the notification.
     *
     * @var string
     */
    public $message;

    /**
     * notification payload
     * 
     * @var array
     */
    protected $notification = [];

    /**
     * data payload
     * 
     * @var array
     */
    public $data = [];

    /**
     * @var null|string
     */
    public $collapse_key;

    /**
     * @var string
     */
    public $priority = self::PRIORITY_NORMAL;

    /**
     * @var null|string
     */
    public $time_to_live;

    /**
     * @param string|null $title
     * @param string|null $message
     * @param array $data
     * @param string $priority
     * @param string|null $collapse_key
     * @param string|null $time_to_live
     */
    public function __construct($title = null, $message = null, $data = [], $priority = self::PRIORITY_NORMAL, $collapse_key = null, $time_to_live = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->priority = $priority;
        $this->collapse_key = $collapse_key;
        $this->time_to_live = $time_to_live;
        $this->notification();
    }

    /**
     * Set the title of the notification.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this->notification();
    }

    /**
     * Set the message of the notification.
     *
     * @param string $message
     *
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this->notification();
    }

    /**
     * create notification payload.
     *
     * @return $this
     */
    public function notification()
    {
        $this->notification = [
            'title' => $this->title,
            'message' => $this->message
        ];

        return $this;
    }

    /**
     * Set the priority of the notification.
     *
     * @param string $priority
     *
     * @return $this
     */
    public function priority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set the time_to_live of the notification.
     *
     * @param int $time_to_live
     *
     * @return $this
     */
    public function time_to_live($time_to_live)
    {
        $this->time_to_live = $time_to_live;

        return $this;
    }

    /**
     * Set the collapse key of the notification.
     *
     * @param string $collapse_key
     *
     * @return $this
     */
    public function collapse_key($collapse_key)
    {
        $this->collapse_key = $collapse_key;

        return $this;
    }

    /**
     * Add data to the notification.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function data($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Override the data of the notification.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $message = [];
        if ($this->notification) 
            $message['notification'] = $this->notification;
        
        if ($this->data) 
            $message['data'] = $this->data;
        
        if ($this->collapse_key) 
            $message['collapse_key'] = $this->collapse_key;
        
        if ($this->priority) 
            $message['priority'] = $this->priority;

        if ($this->time_to_live)
            $message['time_to_live'] = $this->time_to_live;
        
        return $message;
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