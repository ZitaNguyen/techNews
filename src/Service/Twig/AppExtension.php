<?php


namespace App\Service\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('summarize', function( $contenu ) {

                // Suppression des balises HTML
                $string = strip_tags($contenu);

                // Si mon $string est supérieur à 150, je continue
                if (strlen($string) > 150) {
                    // je coupe ma chaine à 150
                    $stringCut = substr($string, 0, 150);
                    // je m'assure de ne pas couper un mot
                    // en recherchant la position du dernier espace
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                }

                return $string . '...';

            }, array( 'is_safe' => array( 'html' ) ) )
        ];
    }

}