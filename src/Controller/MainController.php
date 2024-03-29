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
        ?Request $request,
        ?Response $response
    ): Response
    {
        $index = new \Kvazar\Index\Manticore();

        if ($rss = ('rss' == $request->get('mode')))
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/xml');
        }

        return $this->render(
            $rss ? 'default/main/index.rss.twig' : 'default/main/index.html.twig',
            [
                'request' => $request,
                'records' => $index->get(
                    $request->get('search') ? (string) $request->get('search') : '',
                    [],
                    [
                        'time' => 'desc'
                    ],
                    $request->get('part') > 1 ? ((int) $request->get('part') - 1) * (int) $this->getParameter('app.main.index.limit') : 0,
                    $this->getParameter('app.main.index.limit')
                )
            ],
            $response
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
        ?Request $request,
        ?Response $response
    ): Response
    {
        $index = new \Kvazar\Index\Manticore();

        if ($rss = ('rss' == $request->get('mode')))
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/xml');
        }

        return $this->render(
            $rss ? 'default/main/namespace.rss.twig' : 'default/main/namespace.html.twig',
            [
                'request' => $request,
                'records' => $index->get(
                    $request->get('search') ? (string) $request->get('search') : '',
                    [
                        'crc32_namespace' => crc32(
                            $request->get('namespace')
                        )
                    ],
                    [
                        'time' => 'desc'
                    ],
                    $request->get('part') > 1 ? ((int) $request->get('part') - 1) * (int) $this->getParameter('app.main.index.limit') : 0,
                    $this->getParameter('app.main.index.limit')
                )
            ],
            $response
        );
    }

    #[Route(
        '/{transaction}/{get}',
        name: 'main_transaction',
        requirements:
        [
            'transaction' => '^[A-f0-9]{64}$',
        ],
        defaults:
        [
            'get' => null,
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

        foreach ($index->get(
            '',
            [
                'crc32_transaction' => crc32(
                    $request->get('transaction')
                )
            ]
        ) as $record)
        {
            if ($record['transaction'] === $request->get('transaction'))
            {
                switch ($request->get('get'))
                {
                    case 'key':

                        $response = new Response();

                        $response->headers->set(
                            'Content-length',
                            strlen(
                                $record['key']
                            )
                        );

                        $response->headers->set(
                            'Content-Disposition',
                            sprintf(
                                'attachment; filename="%s.key";',
                                $request->get('transaction')
                            )
                        );

                        $response->sendHeaders();

                        return $response->setContent(
                            $record['key']
                        );

                    case 'value':

                        $response = new Response();

                        $response->headers->set(
                            'Content-length',
                            strlen(
                                $record['value']
                            )
                        );

                        $response->headers->set(
                            'Content-Disposition',
                            sprintf(
                                'attachment; filename="%s.value";',
                                $request->get('transaction')
                            )
                        );

                        $response->sendHeaders();

                        return $response->setContent(
                            $record['value']
                        );

                    default:

                        return $this->render(
                            'default/main/transaction.html.twig',
                            [
                                'request' => $request,
                                'record'  => $record
                            ]
                        );
                }
            }
        }

        throw $this->createNotFoundException();
    }
}