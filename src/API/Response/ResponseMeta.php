<?php

namespace Mytheresa\Challenge\API\Response;

class ResponseMeta
{
    public function __construct(
        private readonly int $count,
        private readonly int $totalCount,
        private readonly int $offset
    )
    {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}