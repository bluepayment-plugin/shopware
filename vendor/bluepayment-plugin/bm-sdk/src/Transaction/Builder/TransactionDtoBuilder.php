<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\Builder;

use BlueMedia\Common\Dto\AbstractDto;
use BlueMedia\Configuration;
use BlueMedia\Hash\HashGenerator;
use BlueMedia\Serializer\Serializer;
use BlueMedia\Transaction\Dto\TransactionDto;

final class TransactionDtoBuilder
{
    public static function build(array $transactionData, Configuration $configuration): AbstractDto
    {
        $serializer = new Serializer();
        $transactionDto = $serializer->serializeDataToDto($transactionData, TransactionDto::class);
        $transactionDto->getTransaction()->setServiceId($configuration->getServiceId());

        $hash = HashGenerator::generateHash(
            $transactionDto->getTransaction()->capitalizedArray(),
            $configuration
        );

        $transactionDto->getTransaction()->setHash($hash);

        return $transactionDto;
    }
}
