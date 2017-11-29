<?php

namespace AppBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param integer   $page
     * @param integer   $limit
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getMessages($page, $limit)
    {
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->getQuery();

        return $this->paginate($query, $page, $limit);
    }


    /**
     * @param $query
     * @param integer   $page
     * @param integer   $limit
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    protected function paginate($query, $page, $limit)
    {
        $paginator = new Paginator($query);
        $paginator->getQuery()->setFirstResult($limit * ($page - 1))->setMaxResults($limit);

        return $paginator;
    }
}
