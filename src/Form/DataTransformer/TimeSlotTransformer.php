<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimeSlotTransformer implements DataTransformerInterface
{
    public function transform($value): mixed
    {
        if (null === $value) {
            return '';
        }

        // Si la valeur est un objet DateTime, on la transforme en chaîne 'H:i'
        if ($value instanceof \DateTime) {
            return $value->format('H:i');
        }

        return $value;
    }

    public function reverseTransform($value): mixed
    {
        if (!$value) {
            return null;
        }

        try {
            // Convertir la chaîne 'H:i' en un objet DateTime
            $dateTime = new \DateTime($value);
        } catch (\Exception $e) {
            throw new TransformationFailedException('Format d\'heure invalide.');
        }

        // Retourner l'objet DateTime
        return $dateTime;
    }
}



