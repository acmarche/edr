<?php

namespace AcMarche\Edr\Page\Message;

final readonly class PageCreated
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
