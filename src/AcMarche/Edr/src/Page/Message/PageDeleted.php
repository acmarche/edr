<?php

namespace AcMarche\Edr\Page\Message;

final readonly class PageDeleted
{
    public function __construct(
        private int $pageId
    ) {
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
