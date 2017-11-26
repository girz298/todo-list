<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TaskGroupFixture extends Fixture
{
    const GROUP_DIPLOMA = 'group_diploma';
    const GROUP_LIFE = 'group_life';
    const GROUP_VACATION = 'group_vacation';
    const GROUP_MUSIC = 'group_music';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /* @var $user User */
        $user = $this->getReference('user');

        foreach ($this->getGroups() as $reference => $groupDescription) {
            $group = new TaskGroup();
            $group
                ->setUser($user)
                ->setDescription($groupDescription);

            $manager->persist($group);

            $this->addReference($reference, $group);
        }

        $manager->flush();
    }

    private function getGroups()
    {
        return [
            self::GROUP_DIPLOMA => 'Диплом',
            self::GROUP_LIFE => 'Жизнь',
            'group_work' => 'Работа',
            'group_business' => 'Бизнес',
            'group_study' => 'Учеба',
            'group_sport' => 'Спорт',
            self::GROUP_MUSIC => 'Музыка',
            self::GROUP_VACATION => 'Отпуск',
        ];
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
