<?php

namespace AppBundle\Event\Listener;

use AppBundle\DTO\MailDTO;
use AppBundle\Entity\Message;
use AppBundle\Event\Event\SendEmailEvent;
use AppBundle\Service\MailService;

class SendEmailListener
{
    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var
     */
    private $emailFrom;

    /**
     * @var
     */
    private $emailAdmin;

    /**
     * SendEmailListener constructor.
     * @param MailService $mailService
     * @param $emailFrom
     * @param $emailAdmin
     */
    public function __construct(MailService $mailService, $emailFrom, $emailAdmin)
    {
        $this->mailService = $mailService;
        $this->emailFrom = $emailFrom;
        $this->emailAdmin = $emailAdmin;
    }

    /**
     * @param SendEmailEvent $event
     */
    public function onSendEmailEvent(SendEmailEvent $event)
    {
        $message = $event->getMessage();

        $this->mailService->sendMail($this->getToUserDTO($message));
        $this->mailService->sendMail($this->getToAdminDTO($message));
    }

    /**
     * @param Message $message
     * @return MailDTO
     */
    private function getToUserDTO(Message $message)
    {
        return new MailDTO(
            'Wellcome!',
            $this->emailFrom,
            $message->getEmail(),
            ['name' => $message->getName()],
            'emails/wellcomeUser.html.twig'
        );
    }

    /**
     * @param Message $message
     * @return MailDTO
     */
    private function getToAdminDTO(Message $message)
    {
        return new MailDTO(
            'New user',
            $this->emailFrom,
            $this->emailAdmin,
            [
                'name' => $message->getName(),
                'email' => $message->getEmail(),
                'phone' => $message->getPhone(),
                'text' => $message->getText(),
                'createdAt' => $message->getCreatedAt(),
            ],
            'emails/new_user_notification.html.twig'
        );
    }

}