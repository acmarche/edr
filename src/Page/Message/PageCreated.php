<?php

namespace AcMarche\Edr\Page\Message;

final class PageCreated
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
