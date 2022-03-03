<?php

namespace AcMarche\Edr\Security\Ldap;

use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Exception\DriverNotFoundException;
use Symfony\Component\Ldap\LdapInterface;

/**
 * Copy/Paste.
 *
 * @see Ldap
 */
class LdapEdr implements LdapInterface
{
    public function __construct(
        private AdapterInterface $adapter
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $dn = null, string $password = null): void
    {
        $this->adapter->getConnection()->bind($dn, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $dn, string $query, array $options = []): QueryInterface
    {
        return $this->adapter->createQuery($dn, $query, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager(): EntryManagerInterface
    {
        return $this->adapter->getEntryManager();
    }

    /**
     * {@inheritdoc}
     */
    public function escape(string $subject, string $ignore = '', int $flags = 0): string
    {
        return $this->adapter->escape($subject, $ignore, $flags);
    }

    /**
     * Creates a new Ldap instance.
     *
     * @param string $adapter The adapter name
     * @param array  $config  The adapter's configuration
     */
    public static function create(string $adapter, array $config = []): self
    {
        if ('ext_ldap' !== $adapter) {
            throw new DriverNotFoundException(sprintf('Adapter "%s" not found. Only "ext_ldap" is supported at the moment.', $adapter));
        }

        return new self(new Adapter($config));
    }
}
