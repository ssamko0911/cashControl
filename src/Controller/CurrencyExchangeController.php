<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\CurrencyApiClientException;
use App\Request\CurrencyApiRequest;
use App\Service\CurrencyApiClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class CurrencyExchangeController extends AbstractController
{
    public function __construct(
        private readonly CurrencyApiClient $apiClient,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/exchange', name: 'currencyExchange', methods: [Request::METHOD_GET])]
    public function exchange(#[MapQueryString] CurrencyApiRequest $request): JsonResponse
    {
        try {
            $currencyApiResponse = $this->apiClient->get($request);
        } catch (CurrencyApiClientException $exception) {
            $this->logger->error("Api client error: {$exception->getMessage()}");
            return new JsonResponse($exception->publicMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'amount' => $currencyApiResponse->rates[$request->toCurrency]['rate_for_amount'],
        ], Response::HTTP_OK);
    }
}
