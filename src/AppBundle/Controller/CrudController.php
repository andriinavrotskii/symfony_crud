<?php

namespace AppBundle\Controller;

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
}
