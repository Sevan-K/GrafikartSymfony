<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RecipeType extends AbstractType
{

    public function __construct(private FormListenerFactory $formListenerFactory) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,  [
                'label' => 'Titre',
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                // 'constraints'   =>
                // new Sequentially([
                //     new Length(min: 10),
                //     new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Invalid slug')
                // ])
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => '',
            ])
            // ->add('createAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('duration', IntegerType::class, [
                'label' => 'Temps de préparation'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    // public function autoSlug(PreSubmitEvent $event)
    // {
    //     FormHelper::makeAutoSlugger()($event);
    // }

    // public function attachTimestamps(PostSubmitEvent $event)
    // {
    //     $data = $event->getData();

    //     if (!($data instanceof Recipe)) {
    //         return;
    //     }

    //     if (!$data->getCreateAt()) {
    //         $data->setCreateAt(new DateTimeImmutable());
    //     }
    //     $data->setUpdatedAt(new DateTimeImmutable());
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            // 'validation_groups' => ['Default', 'Extra']
        ]);
    }
}
