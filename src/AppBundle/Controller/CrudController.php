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
     * @param Request $request
     *
     * @Rest\Post("/api/grid/create", name="create_grid_item")
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            return new JsonResponse('fail');
        }

        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);


//        return $this->myResponce([
//            'getData' => $form->getData(),
//            'isSubmitted' => $form->isSubmitted(),
//            'isValid' => $form->isValid(),
//            'errors' => $form->getErrors(true),
//            'token_manager' => $this->get('security.csrf.token_manager')->getToken('app_bundle_message_type')->getValue(),
//            'session' => $this->get('session')->all(),
//        ]);



        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return new JsonResponse([], 200);
        }

        return $this->myResponse(['errors' => $this->getErrorMessages($form)], 400);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    private function myResponse($data)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($data, 'json'), 200, [], true);
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
