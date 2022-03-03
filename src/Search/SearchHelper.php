<?php

namespace AcMarche\Edr\Search;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SearchHelper
{
    public const ENFANT_LIST = 'enfant_list';
    public const TUTEUR_LIST = 'tuteur_list';
    public const PRESENCE_LIST = 'presence_list';
    public const PRESENCE_LIST_BY_MONTH = 'presence_list_by_month';
    public const MESSAGE_INDEX = 'message_index';

    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function saveSearch(string $name, array $args): void
    {
        $this->session->set($name, $args);
    }

    public function getArgs(string $name): array
    {
        return $this->session->get($name, []);
    }

    public function deleteSearch(string $name): void
    {
        $this->session->remove($name);
    }
}
