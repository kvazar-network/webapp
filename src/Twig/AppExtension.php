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
            ),
            new TwigFilter(
                'format_type',
                [
                    $this,
                    'formatType'
                ]
            ),
            new TwigFilter(
                'namespace_title',
                [
                    $this,
                    'namespaceTitle'
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

    public function formatType(
        mixed $value,
        int $code
    ): string
    {
        switch ($code)
        {
            case \Kvazar\Index\Manticore::TYPE_NULL:

                return '[null]';

            case \Kvazar\Index\Manticore::TYPE_BOOL:

                return sprintf(
                    '[bool:%s]',
                    $value ? 'true' : 'false'
                );

            case \Kvazar\Index\Manticore::TYPE_INT:

                return sprintf(
                    '[int:%d]',
                    $value
                );

            case \Kvazar\Index\Manticore::TYPE_FLOAT:

                return sprintf(
                    '[float:%s]',
                    $value
                );

            case \Kvazar\Index\Manticore::TYPE_STRING:

                return (string) $value;

            case \Kvazar\Index\Manticore::TYPE_BIN:

                return '[binary]';

            case \Kvazar\Index\Manticore::TYPE_JSON:

                return '[json]';

            case \Kvazar\Index\Manticore::TYPE_XML:

                return '[xml]';

            case \Kvazar\Index\Manticore::TYPE_BASE_64:

                return '[base64]';

            case \Kvazar\Index\Manticore::TYPE_ARRAY:

                return '[array]';

            case \Kvazar\Index\Manticore::TYPE_OBJECT:

                return '[object]';

            default:
                return '[undefined]';
        }
    }

    public function namespaceTitle(
        string $namespace
    ): string
    {
        $index = new \Kvazar\Index\Manticore();

        $results = $index->get(
            '_KEVA_NS_',
            [
                'crc32_namespace' => crc32(
                    $namespace
                )
            ]
        );

        if ($results)
        {
            foreach ($results as $result)
            {
                if ($result['key'] == '_KEVA_NS_' && !empty($result['value']))
                {
                    return trim(
                        $result['value']
                    );
                }
            }
        }

        return $namespace;
    }
}