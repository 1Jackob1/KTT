<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, [
                'required' => false,
            ])
            ->add('firstName')
            ->add('secondName')
            ->add('lastName', TextType::class, [
                'required' => false,
            ])
            ->add('timezone')
            ->add('tasks', EntityType::class, [
                'multiple' => true,
                'class' => Task::class,
                'required' => false,
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'by_reference' => false,
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'user_type';
    }
}
