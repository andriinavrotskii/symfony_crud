<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 07.12.17
 * Time: 13:21
 */

namespace AppBundle\Service;


use AppBundle\Entity\Message;
use AppBundle\Event\Event\SendEmailEvent;
use AppBundle\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * GridService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, FormFactory $formFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveMessage(Request $request)
    {
        $formData = $request->get('app_bundle_message_type');

        if (!empty($formData['id'])) {
            $message = $this->em->getRepository('AppBundle:Message')->find($formData['id']);
        } else {
            $message = new Message();
        }

        $form = $this->formFactory->create(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($message);
            $this->em->flush();

            $this->eventDispatcher->dispatch(
                SendEmailEvent::NAME, new SendEmailEvent($message)
            );

            return [];
        }

        return ['errors' => $this->getErrorMessages($form)];
    }

    /**
     * @param Form $form
     * @return array
     */
    protected function getErrorMessages(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        /** @var Form $child*/
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * @param Message $message
     * @return array
     */
    public function deleteMessage(Message $message)
    {
        if (!$message) {
            throw new NotFoundHttpException('No message found');
        }

        $this->em->remove($message);
        $this->em->flush();

        return [];
    }
}