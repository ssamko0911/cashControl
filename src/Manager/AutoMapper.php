<?php

namespace App\Manager;

use App\DTO\CurrencyDTO;
use App\DTO\DTOInterface;
use App\DTO\MoneyDTO;
use App\DTO\PropertyDTO;
use App\Entity\EntityInterface;
use Doctrine\Common\Collections\Collection;
use Money\Money;
use Random\RandomException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

class AutoMapper
{
    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function mapToModel(
        EntityInterface $entity,
        string $accessGroup
    ): DTOInterface {
        $dto = $entity->getDTO();

        $props = $this->getPropertiesWithTypesAndGroups($dto, $accessGroup, false);

        /** @var PropertyDTO $property */
        foreach ($props as $property) {
            $getter = $this->getGetterMethod($entity, $property->name);
            $value = $entity->{$getter}();

            if ($value instanceof Collection) {
                $valueArray = [];
                foreach ($value as $item) {
                    $valueArray[] = $this->mapToModel($item, $accessGroup);
                }
                $dto->{$property->name} = $valueArray;
            } elseif ($property->dto && $value) {
                $dto->{$property->name} = $this->mapToModel($value, $accessGroup);
            } elseif ($property->transform) {
                $dto->{$property->name} = $property->transform->getReverseTransform($value);
            } elseif ($value instanceof Money) {
                $currencyDto = new CurrencyDTO();
                $currencyDto->code = $value->getCurrency()->getCode();

                $amountDTO = new MoneyDTO();
                $amountDTO->amount = $value->getAmount();
                $amountDTO->currency = $currencyDto;

                $dto->{$property->name} = $amountDTO;
            } else {
                $dto->{$property->name} = $value;
            }
        }

        return $dto;
    }

    private function getGetterMethod(EntityInterface $entity, string $property): ?string
    {
        $getters = [
            'get' . ucfirst($property),
            'is' . ucfirst($property),
            'has' . ucfirst($property),
        ];

        return $this->validateMethod($entity, $getters);
    }

    /**
     * @throws \ReflectionException
     */
    private function getPropertiesWithTypesAndGroups(
        DTOInterface $dto,
        string $accessGroup,
        bool $checkInitialized
    ): array {
        $serializerClassMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $serializerExtractor = new SerializerExtractor($serializerClassMetadataFactory);
        $reflectionExtractor = new ReflectionExtractor();

        $props = $serializerExtractor->getProperties($dto::class, [
            'serializer_groups' => [$accessGroup],
        ]);

        $list = [];

        foreach ($props as $prop) {
            $refProperty = new \ReflectionProperty($dto::class, $prop);
            if ($checkInitialized && ! $refProperty->isInitialized($dto)) {
                continue;
            }
            if ($refProperty->isPrivate()) {
                continue;
            }
            $property = new PropertyDTO();
            $property->name = $prop;
            $propertyType = current($reflectionExtractor->getTypes($dto::class, $prop));
            if (!$this->isMoneyObject($propertyType)) {
                if (
                    $propertyType->getBuiltinType() === Type::BUILTIN_TYPE_OBJECT
                    && ! enum_exists($propertyType->getClassName()) && new ($propertyType->getClassName())(
                    ) instanceof DTOInterface
                ) {
                    if ($checkInitialized) {
                        if ($dto->{$prop} !== null) {
                            $property->dto = true;
                        }
                    } else {
                        $property->dto = true;
                    }
                }
            }
            $list[] = $property;
        }

        return $list;
    }

    private function validateMethod(EntityInterface $entity, array $methods): ?string
    {
        $availableMethods = array_filter($methods, static fn (string $method): bool => method_exists($entity, $method));
        if (count($availableMethods) > 1) {
            throw new \LogicException(sprintf('Entity class \'%s\' has multiple same methods - this is insane!', $entity::class));
        }

        $method = current($availableMethods);
        if (! $method) {
            throw new \BadMethodCallException(sprintf('Entity class \'%s\' does not have any of the attempted methods. Available methods - \'%s\'. Attempted methods were: \'%s\'', $entity::class, implode(',', get_class_methods($entity)), implode(', ', $methods)));
        }

        return $method;
    }

    private function isMoneyObject(Type $propertyType): bool
    {
        return $propertyType->getClassName() === 'Money\Money';
    }
}