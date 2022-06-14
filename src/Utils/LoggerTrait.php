<?php

namespace Mytheresa\Challenge\Utils;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Simple trait to reuse default logger across application
 */
trait LoggerTrait
{
    private LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}