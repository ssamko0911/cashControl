<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\CurrencyApiResponse;
use App\Exception\CurrencyApiClientException;
use App\Request\CurrencyApiRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class CurrencyApiClient
{
    private Serializer $serializer;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface     $logger,
        private readonly string              $apiUrl,
        private readonly string              $apiKey,
    )
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    /**
     * @throws CurrencyApiClientException
     */
    public function get(CurrencyApiRequest $request): CurrencyApiResponse
    {
        $this->logger->info("Query params: ", [
            'fromCurrency' => $request->fromCurrency,
            'toCurrency' => $request->toCurrency,
            'amount' => $request->amount,
        ]);

        try {
            $response = $this->httpClient->request(
                'GET',
                "$this->apiUrl"
                . "?api_key=$this->apiKey&from="
                . "$request->fromCurrency&to="
                . "$request->toCurrency&amount="
                . "$request->amount&format="
                . "json"
            );

            $content = $response->getContent();
        } catch (Throwable $exception) {
            throw new CurrencyApiClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this->serializer->deserialize($content, CurrencyApiResponse::class, 'json');
    }
}