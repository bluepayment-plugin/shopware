<?php
declare(strict_types=1);

namespace BlueMedia\HttpClient;

use BlueMedia\Common\Dto\AbstractDto;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    /**
     * Perform POST request.
     *
     * @param AbstractDto $requestDto
     *
     * @return ResponseInterface
     */
    public function post(AbstractDto $requestDto): ResponseInterface;
}
