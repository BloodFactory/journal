<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Organization;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class DailyReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->setAction('add_daily_report')
            ->add('organization', EntityType::class, [
                'disabled' => true,
                'label' => 'Организация',
                'class' => Organization::class,
                'choice_label' => 'name'
            ])
            ->add('total', NumberType::class, [
                'label' => 'Фактическая численность'
            ])
            ->add('atWork', NumberType::class, [
                'label' => 'На рабочем месте'
            ])
            ->add('onHoliday', NumberType::class, [
                'label' => 'В отпуске'
            ])
            ->add('remoteTotal', NumberType::class, [
                'label' => 'Общее количество работников на дистанционной форме работы'
            ])
            ->add('remotePregnant', NumberType::class, [
                'label' => 'Беременные женщины на дистанционной форме работы'
            ])
            ->add('remoteWithChildren', NumberType::class, [
                'label' => 'Женщины с детьми в возрасте до 14 лет на дистанционной форме работы'
            ])
            ->add('remoteOver60', NumberType::class, [
                'label' => 'Работники старше 60 лет на дистанционной форме работы'
            ])
            ->add('onTwoWeekQuarantine', NumberType::class, [
                'label' => 'На карантине'
            ])
            ->add('onSickLeave', NumberType::class, [
                'label' => 'На больничном'
            ])
            ->add('sickCOVID', NumberType::class, [
                'label' => 'Заболевших (COVID-19)'
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'label' => 'Примечание',
                'empty_data' => null
            ]);
    }
}