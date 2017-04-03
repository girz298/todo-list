<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 02.04.17
 * Time: 16:49
 */

namespace AppBundle\Form\Task;

use AppBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BaseTaskType extends AbstractType
{
    // TODO If TYPE_DAILY_GOAL we show status select box
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px; height: 100px;'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ],
                'choices'  => [
                    'Daily Goal' => Task::TYPE_DAILY_GOAL,
                    'Weekly Goal' => Task::TYPE_WEEKLY_GOAL,
                    'Intermediate Goal' => Task::TYPE_INTERMEDIATE_GOAL,
                    'Long Range Goal' => Task::TYPE_LONG_RANGE_GOAL,
                    'Governing Value' => Task::TYPE_GOVERNING_VALUE,
                ],
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ],
                'choices'  => [
                    'Urgent' => Task::STATUS_IMPORTANT_URGENT,
                    'Not Urgent' => Task::STATUS_IMPORTANT_NOT_URGENT,
                    'Urgent ' => Task::STATUS_NOT_IMPORTANT_URGENT,
                    'Not Urgent ' => Task::STATUS_NOT_IMPORTANT_NOT_URGENT
                ],
                'group_by' => function($val, $key, $index) {
                    if ($val < 3) {
                        return 'Important';
                    } else {
                        return 'Not Important';
                    }
                },
            ])
            ->add('end_date', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
                    'style' => 'margin-bottom:15px'
                ]
            ]);
    }
}