<?php



namespace App\Form;

use App\Entity\SeanceEnregistrement;
use App\Form\DataTransformer\TimeSlotTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer la durée depuis les options du formulaire
        $duree = $options['duree'];
        
        // Initialiser le transformateur de créneaux horaires
        $timeSlotTransformer = new TimeSlotTransformer();

        // Construire le formulaire
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('date', DateType::class, ['label' => 'Date'])
            ->add('heure_debut', ChoiceType::class, [
                'label' => 'Heure de début',
                'choices' => array_combine(
                    array_keys($options['available_time_slots']), // Passer les créneaux horaires comme option
                    array_keys($options['available_time_slots'])  // Clés comme labels des choix
                ),
            ])
            ->get('heure_debut') // Ajouter le transformateur sur ce champ
            ->addModelTransformer($timeSlotTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeanceEnregistrement::class,
            'duree' => 1, // Défaut de la durée
            'available_time_slots' => [], // Option pour les créneaux horaires
        ]);
    }
}







