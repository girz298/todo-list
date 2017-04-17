<?php

namespace AppBundle\Repository;


use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class TaskGroupRepository
 * @package AppBundle\Repository
 */
class TaskGroupRepository extends EntityRepository
{

    /**
     * @param User $user
     * @param $id
     * @return TaskGroup
     */
    public function getByUserAndId(User $user, $id)
    {
        return current(
            $this->createQueryBuilder('tg')
                ->select('tg')
                ->where('tg.user=' . $user->getId())
                ->andWhere('tg.id=' . $id)
                ->getQuery()
                ->getResult()
        );
    }
}