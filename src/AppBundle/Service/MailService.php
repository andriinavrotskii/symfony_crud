<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 07.12.17
 * Time: 10:32
 */

namespace AppBundle\Service;

use AppBundle\DTO\MailDTO;
use Symfony\Component\Templating\EngineInterface;

class MailService
{
    /**
     * @var MailDTO
     */
    private $mailData;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Templating
     */
    private $templating;

    /**
     * MailService constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param MailDTO $mailData
     * @return float
     */
    public function sendMail(MailDTO $mailData)
    {
        try {
            $message = (new \Swift_Message($mailData->getSubject()))
                ->setFrom($mailData->getFrom())
                ->setTo($mailData->getTo())
                ->setBody(
                    $this->templating->render(
                        $mailData->getView(),
                        $mailData->getBodyData()
                    ),
                    $mailData->getType()
                );

            return $this->mailer->send($message);
        } catch (\Exception $e) {
            // TODO write to log
            dump($e->getMessage());
        }
    }
}