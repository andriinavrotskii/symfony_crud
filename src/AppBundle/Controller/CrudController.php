<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Service\GridService;
use AppBundle\Service\MessageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
//    const LIMIT_ITEMS_ON_PAGE = 10;
//
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
     * @param int option $page
     *
     * @Rest\Get("/api/grid", name="get_grid")
     * @Rest\Get("/api/grid/{page}", name="get_grid_page")
     *
     * @return JsonResponse
     */
    public function getGridAction(Request $request, GridService $gridService, $page = 1)
    {
        if(!$request->isXmlHttpRequest()) {
            return $this->getJsonResponse('fail', 403);
        }

        return $this->getJsonResponse(
            $gridService->getGridData($page)
        );
    }

    /**
     * @param Message $message
     *
     * @Rest\Get("/api/message/{id}", name="get_message")
     *
     * @return JsonResponse
     */
    public function getMessage(Request $request, Message $message)
    {
        $this->createNotFoundException('No message found');
        if(!$request->isXmlHttpRequest()) {
            return $this->getJsonResponse('fail', 403);
        }

        return $this->getJsonResponse(['message' => $message], 200);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/api/message", name="save_message")
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request, MessageService $messageService)
    {
        if(!$request->isXmlHttpRequest()) {
            return $this->getJsonResponse('fail', 403);
        }

        return $this->getJsonResponse(
            $messageService->saveMessage($request)
        );
    }


    /**
     * @param Request $request
     *
     * @Rest\Delete("/api/message/{id}", name="delete_grid_item")
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, MessageService $messageService, Message $message)
    {
        if(!$request->isXmlHttpRequest()) {
            return $this->getJsonResponse('fail', 403);
        }

        return $this->getJsonResponse(
            $messageService->deleteMessage($message),
            204
        );
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    private function getJsonResponse($data, $code = 200)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($data, 'json'), $code, [], true);
    }
}
