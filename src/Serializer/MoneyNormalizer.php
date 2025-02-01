<?php

declare(strict_types=1);

namespace App\Serializer;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Money;
use Money\Parser\IntlLocalizedDecimalParser;
use NumberFormatter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MoneyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    )
    {
    }

    private function getFormatter(): IntlLocalizedDecimalFormatter
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new NumberFormatter($this->parameterBag->get('default_locale'), NumberFormatter::DECIMAL);

        return new IntlLocalizedDecimalFormatter($numberFormatter, $currencies);
    }

    private function getParser(): IntlLocalizedDecimalParser
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new NumberFormatter($this->parameterBag->get('default_locale'), NumberFormatter::DECIMAL);

        return new IntlLocalizedDecimalParser($numberFormatter, $currencies);
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Money) {
            throw new \InvalidArgumentException('The object must be an instance of Money');
        }

        return [
            'amount' => [
                'amount' => $this->getFormatter()->format($object),
                'currency' => [
                    'code' => $object->getCurrency()->getCode(),
                ],
            ],
        ];
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Money
    {
        if (!isset($data['amount']['amount'], $data['amount']['currency']['code'])) {
            throw new \InvalidArgumentException('Invalid money data structure');
        }

        $amount = $data['amount']['amount'];
        $currencyCode = $data['amount']['currency']['code'];

        return $this->getParser()->parse($amount, $currencyCode);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Money;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Money::class && isset($data['amount']['amount'], $data['amount']['currency']['code']);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Money::class => true,
        ];
    }
}
