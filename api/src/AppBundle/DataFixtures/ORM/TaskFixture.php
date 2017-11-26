<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->getTasks() as $groupReference => $tasks) {
            $group = $this->getReference($groupReference);

            foreach ($tasks as $taskData) {
                $this->createNewTask($taskData, $group);
            }
        }

        $this->manager->flush();
    }

    private function createNewTask(array $taskData, TaskGroup $group) {
        $task = new Task();
        $task
            ->setDescription($taskData['d'])
            ->setGroup($group)
            ->setEndDate(new \DateTime('+1 week'))
            ->setType($taskData['t']);

        if ($taskData['s'] ?? null) {
            $task->setStatus($taskData['s']);
        }

        if ($taskData['c'] ?? null) {
            foreach ($taskData['c'] as $childData) {
                $task->addChild($this->createNewTask($childData, $group));
            }
        }

        $this->manager->persist($task);

        return $task;
    }

    private function getTasks()
    {
        return [
            TaskGroupFixture::GROUP_DIPLOMA => [
                [
                    'd' => 'Сделать первую процентовку',
                    't' => Task::TYPE_WEEKLY_GOAL,
                    'c' => [
                        [
                            'd' => 'Сделать первую часть диплома',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Сделать вторую часть диплома',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Сделать третью часть диплома',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Сдать отчет по практике',
                            't' => Task::TYPE_DAILY_GOAL
                        ],
                    ]
                ],
                [
                    'd' => 'Сделать вторую процентовку',
                    't' => Task::TYPE_WEEKLY_GOAL,
                    'c' => [
                        [
                            'd' => 'Нарисовать плакат',
                            't' => Task::TYPE_DAILY_GOAL
                        ],
                        [
                            'd' => 'Нарисовать блок-схему',
                            't' => Task::TYPE_DAILY_GOAL
                        ]
                    ]
                ],
                [
                    'd' => 'Написать программу',
                    't' => Task::TYPE_INTERMEDIATE_GOAL
                ],
                [
                    'd' => 'Сделать дизайн',
                    't' => Task::TYPE_INTERMEDIATE_GOAL,
                    'c' => [
                        [
                            'd' => 'Сделать дизайн главной страницы',
                            't' => Task::TYPE_WEEKLY_GOAL,
                            'c' => [
                                [
                                    'd' => 'Сделать дизайн хэдера',
                                    't' => Task::TYPE_DAILY_GOAL,
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    'd' => 'Разработать серверную архитектуру',
                    't' => Task::TYPE_WEEKLY_GOAL,
                    'c' => [
                        [
                            'd' => 'Настроить контейнер для PHP',
                            't' => Task::TYPE_DAILY_GOAL,
                        ],
                        [
                            'd' => 'Настроить контейнер для Angular 2',
                            't' => Task::TYPE_DAILY_GOAL,
                        ],
                        [
                            'd' => 'Настроить контейнер для MySQL',
                            't' => Task::TYPE_DAILY_GOAL,
                        ],
                        [
                            'd' => 'Настроить контейнер для Proxy',
                            't' => Task::TYPE_DAILY_GOAL,
                        ],
                        [
                            'd' => 'Настроить контейнер для PHPUnit',
                            't' => Task::TYPE_DAILY_GOAL,
                        ],
                    ]
                ],
            ],
            TaskGroupFixture::GROUP_LIFE => [
                [
                    'd' => 'Сходить в кино с друзьями',
                    's' => Task::STATUS_IMPORTANT_URGENT,
                    't' => Task::TYPE_DAILY_GOAL,
                ],
                [
                    'd' => 'Прочитать первую главу книги',
                    's' => Task::STATUS_IMPORTANT_URGENT,
                    't' => Task::TYPE_DAILY_GOAL,
                ],
                [
                    'd' => 'Прочитать вторую главу книги',
                    's' => Task::STATUS_IMPORTANT_URGENT,
                    't' => Task::TYPE_DAILY_GOAL,
                ],
                [
                    'd' => 'Поздравить деда с днем рождения',
                    't' => Task::TYPE_DAILY_GOAL,
                ],
                [
                    'd' => 'Сдать анализы',
                    't' => Task::TYPE_DAILY_GOAL,
                ],
                [
                    'd' => 'Отремонтировать велосипед',
                    't' => Task::TYPE_GOVERNING_VALUE,
                ]
            ],
            TaskGroupFixture::GROUP_VACATION => [
                [
                    'd' => 'Запланировать отдых',
                    't' => Task::TYPE_WEEKLY_GOAL,
                    'c' => [
                        [
                            'd' => 'Купить билеты на самолет',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Купить билеты на поезд',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Забронироать отель',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Забронировать такси',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ]
                    ]
                ],
                [
                    'd' => 'Написать план на экскурсии',
                    't' => Task::TYPE_WEEKLY_GOAL,
                ]
            ],
            TaskGroupFixture::GROUP_MUSIC => [
                [
                    'd' => 'Пройти первый урок по игре на гитаре',
                    't' => Task::TYPE_WEEKLY_GOAL,
                    'c' => [
                        [
                            'd' => 'Научиться играть G аккорд',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                        [
                            'd' => 'Научиться играть D аккорд',
                            't' => Task::TYPE_DAILY_GOAL,
                            's' => Task::STATUS_IMPORTANT_URGENT,
                        ],
                    ]
                ],
                [
                    'd' => 'Заменить струны',
                    't' => Task::TYPE_INTERMEDIATE_GOAL,
                ]
            ]
        ];
    }

    public function getDependencies()
    {
        return [
            TaskGroupFixture::class
        ];
    }
}
