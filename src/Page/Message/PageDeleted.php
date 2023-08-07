<?php

namespace AcMarche\Edr\Page\Message;

final class PageDeleted
{
    public function __construct(
        private readonly int $pageId
    ) {
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
