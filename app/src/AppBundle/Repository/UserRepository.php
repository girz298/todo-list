<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr\Join;

use Symfony\Component\Form\Form;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Task $task
     * @return User
     */
    public function getTaskCreatorUser(Task $task)
    {
        $result = $this
            ->createQueryBuilder('u')
            ->select()
            ->leftJoin('u.taskGroups', 'tg',Join::WITH, 'tg.user=u.id')
            ->leftJoin('tg.tasks','tsk',Join::WITH, 'tsk.group=tg.id')
            ->where('tsk.id='.$task->getId())
            ->getQuery()
            ->getResult();
        return current($result);
    }
}
