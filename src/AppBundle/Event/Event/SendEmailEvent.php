<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 06.12.17
 * Time: 15:49
 */

namespace AppBundle\Event\Event;

use AppBundle\Entity\Message;
use Symfony\Component\EventDispatcher\Event;

class SendEmailEvent extends Event
{
    public const NAME = 'send_email.event';

    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}