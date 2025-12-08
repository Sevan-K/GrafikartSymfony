<?php

namespace App\Form;

use App\Controller\Helper\FormHelper;
use App\Entity\Category;
use App\Entity\Recipe;
use MultipleIterator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{

    public function __construct(private FormListenerFactory $formListenerFactory) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'required' => false,
            ])
            // ->add('recipes', EntityType::class, [
            //     'class' => Recipe::class,
            //     'choice_label' => 'title',
            //     'multiple' => true,
            //     'by_reference' => false, // not to use setter but add or remove methods
            //     'expanded' => true,
            // ])
            // ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoslug(...))
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
            ->add('save', SubmitType::class, ['label' => 'Envoyer'])
        ;
    }


    public function autoslug(PreSubmitEvent $event)
    {
        FormHelper::makeAutoSlugger('name')($event);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
