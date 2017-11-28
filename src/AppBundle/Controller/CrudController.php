<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CrudController extends Controller
{
    const LIMIT_ITEMS_ON_PAGE = 10;

    /**
     * @param Request $request
     * @Route("/", name="crud")
     *
     * @return View
     */
    public function indexAction(Request $request)
    {
        $messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findAll();
        return $this->render('crud/base.html.twig', ['messages' => $messages]);
    }


    /**
     * @param Request $request
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

        return new JsonResponse($serializer->serialize($data, 'json'), 200, [], true);
    }

    /**
     * @param Request $request
     * @Rest\Post("/api/grid/create", name="create_grid_item")
     */
    public function createAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            return new JsonResponse('fail');
        }

        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        dump($form->getData()); die;


        return new JsonResponse((string) $form->getData(), 200);
    }
}
