<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return
        [
            new TwigFilter(
                'jIdenticon',
                [
                    $this,
                    'jIdenticon'
                ]
            )
        ];
    }

    public function jIdenticon(
        mixed  $value,
        int    $size  = 48,
        array  $style =
        [
            'backgroundColor' => 'rgba(255, 255, 255, 0)',
            'padding' => 0
        ],
        string $format = 'webp'
    ): string
    {
        $identicon = new \Jdenticon\Identicon();

        $identicon->setValue(
            $value
        );

        $identicon->setSize(
            $size
        );

        $identicon->setStyle(
            $style
        );

        return $identicon->getImageDataUri(
            $format
        );
    }
}