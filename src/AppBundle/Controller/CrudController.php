<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CrudController extends Controller
{
    const LIMIT_ITEMS_ON_PAGE = 10;

    /**
     * @param Request $request
     *
     * @Route("/", name="crud")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('crud/base.html.twig');
    }


    /**
     * @param Request $request
     *
     * @Rest\Get("/api/grid", name="get_grid")
     * @Rest\Get("/api/grid/{page}", name="get_grid_page")
     *
     * @return JsonResponse
     */
    public function getGridAction($page = 1)
    {
        $messages = $this->getDoctrine()->getRepository('AppBundle:Message')
            ->getMessages($page, self::LIMIT_ITEMS_ON_PAGE);

        $data = [
            'messages' => $messages,
            'pages' => round($messages->count() / self::LIMIT_ITEMS_ON_PAGE),
            'page' => $page,
        ];

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return $this->myResponse($data);
    }

    /**
     * @param Message $message
     *
     * @Rest\Get("/api/message/{id}", name="get_message")
     *
     * @return JsonResponse
     */
    public function getMessage(Message $message)
    {
        return $this->myResponse(['message' => $message], 200);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/api/message", name="save_message")
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            return new JsonResponse('fail');
        }

        $em = $this->getDoctrine()->getManager();

        $formData = $request->get('app_bundle_message_type');

        if (isset($formData['id']) && !empty($formData['id'])) {
            $message = $em->getRepository('AppBundle:Message')->find($formData['id']);
        } else {
            $message = new Message;
        }

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();

            return new JsonResponse([]);
        }

        return $this->myResponse(['errors' => $this->getErrorMessages($form)], 200);
    }


    /**
     * @param Request $request
     *
     * @Rest\Delete("/api/message/{id}", name="delete_grid_item")
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Message $message)
    {
        if (!$message) {
            throw $this->createNotFoundException('No message found');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($message);
        $em->flush();

        return $this->myResponse([], 204);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    private function myResponse($data, $code = 200)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($data, 'json'), $code, [], true);
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

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
