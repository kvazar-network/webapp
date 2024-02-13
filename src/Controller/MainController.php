<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route(
        '/{part}',
        name: 'main_index',
        requirements:
        [
            'namespace' => '^N[A-z0-9]{33}$',
            'part' => '^[\d]+$',
        ],
        defaults:
        [
            'part' => 1,
        ],
        methods:
        [
            'GET'
        ]
    )]
    public function index(
        ?Request $request
    ): Response
    {
        $index = new \Kvazar\Index\Manticore();

        return $this->render(
            'default/main/index.html.twig',
            [
                'request' => $request,
                'records' => $index->get(
                    $request->get('search') ? (string) $request->get('search') : '',
                    [],
                    [
                        'time' => 'desc'
                    ],
                    $request->get('part') > 1 ? (int) $request->get('part') * $this->getParameter('app.main.index.limit') : 0,
                    $this->getParameter('app.main.index.limit')
                )
            ]
        );
    }

    #[Route(
        '/{namespace}/{part}',
        name: 'main_namespace',
        requirements:
        [
            'namespace' => '^N[A-z0-9]{33}$',
            'part' => '^[\d]+$',
        ],
        defaults:
        [
            'part' => 1,
        ],
        methods:
        [
            'GET'
        ]
    )]
    public function namespace(
        ?Request $request
    ): Response
    {
        $index = new \Kvazar\Index\Manticore();

        return $this->render(
            'default/main/namespace.html.twig',
            [
                'request' => $request,
                'records' => $index->get(
                    $request->get('search') ? (string) $request->get('search') : '',
                    [
                        'crc32namespace' => crc32(
                            $request->get('namespace')
                        )
                    ],
                    [
                        'time' => 'desc'
                    ],
                    $request->get('part') > 1 ? (int) $request->get('part') * $this->getParameter('app.main.index.limit') : 0,
                    $this->getParameter('app.main.index.limit')
                ),
                'title' => $this->_title(
                    $request->get('namespace')
                )
            ]
        );
    }

    #[Route(
        '/{transaction}',
        name: 'main_transaction',
        requirements:
        [
            'transaction' => '^[A-f0-9]{64}$',
        ],
        methods:
        [
            'GET'
        ]
    )]
    public function transaction(
        ?Request $request
    ): Response
    {
        $index = new \Kvazar\Index\Manticore();

        $records = $index->get(
            '',
            [
                'crc32transaction' => crc32(
                    $request->get('transaction')
                )
            ]
        );

        if (empty($records))
        {
            throw $this->createNotFoundException();
        }

        $record = reset(
            $records
        );

        return $this->render(
            'default/main/transaction.html.twig',
            [
                'request' => $request,
                'record'  => $record,
                'title'   => $this->_title(
                    $record['namespace']
                )
            ]
        );
    }

    private function _title(string $namespace): ?string
    {
        $index = new \Kvazar\Index\Manticore();

        $results = $index->get(
            '_KEVA_NS_',
            [
                'crc32namespace' => crc32(
                    $namespace
                )
            ]
        );

        if ($results)
        {
            foreach ($results as $result)
            {
                if ($result['key'] == '_KEVA_NS_')
                {
                    return trim(
                        $result['value']
                    );
                }
            }
        }

        return null;
    }
}