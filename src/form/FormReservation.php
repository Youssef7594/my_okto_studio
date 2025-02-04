<?php

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('date', DateType::class, ['label' => 'Date'])
            ->add('heure_debut', TimeType::class, ['label' => 'Heure de début'])
            ->add('heure_fin', TimeType::class, ['label' => 'Heure de fin'])
            ->add('tarif', EntityType::class, [
                'class' => Tarif::class,
                'choice_label' => 'description',
                'label' => 'Tarif',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeanceEnregistrement::class,
        ]);
    
}
}