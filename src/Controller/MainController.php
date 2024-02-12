<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route(
        '/',
        name: 'main_index',
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

        return $this->render(
            'default/main/transaction.html.twig',
            [
                'request' => $request,
                'record'  => reset(
                    $records
                )
            ]
        );
    }
}