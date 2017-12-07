<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 07.12.17
 * Time: 13:14
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManagerInterface;

class GridService
{
    const LIMIT_ITEMS_ON_PAGE = 10;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * GridService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getGridData($page)
    {
        $messages = $this->em->getRepository('AppBundle:Message')
            ->getMessages($page, self::LIMIT_ITEMS_ON_PAGE);

        return [
            'messages' => $messages,
            'pages' => round($messages->count() / self::LIMIT_ITEMS_ON_PAGE),
            'page' => $page,
        ];
    }
}