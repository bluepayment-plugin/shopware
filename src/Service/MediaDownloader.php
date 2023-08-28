<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Service;

use Shopware\Core\Checkout\Payment\PaymentMethodDefinition;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class MediaDownloader
{
    private const DEFAULT_EXTENSION = 'jpg';

    private MediaService $mediaService;

    private EntityRepositoryInterface $mediaRepository;

    public function __construct(
        MediaService $mediaService,
        EntityRepositoryInterface $mediaRepository
    ) {
        $this->mediaService = $mediaService;
        $this->mediaRepository = $mediaRepository;
    }

    public function download(string $url, Context $context): ?string
    {
        $existingMedia = $this->getMediaEntity($url, $context);

        if (null !== $existingMedia) {
            return $existingMedia->getId();
        }

        $explodedUrl = explode('.', $url);
        $extension = end($explodedUrl) ?? self::DEFAULT_EXTENSION;
        $file = $this->fetchFromURL($url, $extension);

        if (null === $file) {
            return null;
        }

        return $this->mediaService->saveMediaFile(
            $file,
            $this->buildFileName($url),
            $context,
            PaymentMethodDefinition::ENTITY_NAME,
            null,
            false
        );
    }

    private function getMediaEntity(string $url, Context $context): ?MediaEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('fileName', $this->buildFileName($url)));
        $criteria->addFilter(
            new EqualsFilter('mediaFolder.defaultFolder.entity', PaymentMethodDefinition::ENTITY_NAME)
        );

        $media = $this->mediaRepository->search($criteria, $context)->first();

        if ($media instanceof MediaEntity) {
            return $media;
        }

        return null;
    }

    private function buildFileName(string $string): string
    {
        return sprintf('autopay-payment-%s', md5($string));
    }

    /**
     * Borrowed from
     * \Shopware\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\MediaSerializer::fetchFileFromURL
     */
    private function fetchFromURL(string $url, string $extension): ?MediaFile
    {
        $request = new Request();
        $request->query->set('url', $url);
        $request->query->set('extension', $extension);
        $request->request->set('url', $url);
        $request->request->set('extension', $extension);
        $request->headers->set('content-type', 'application/json');

        try {
            $file = $this->mediaService->fetchFile($request);
            if ($file->getFileSize() > 0) {
                return $file;
            }
        } catch (Throwable $throwable) {
            return null;
        }

        return null;
    }
}
