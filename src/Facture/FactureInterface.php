<?php

namespace AcMarche\Edr\Facture;

use AcMarche\Edr\Entity\Facture\FactureComplement;
use AcMarche\Edr\Entity\Facture\FactureDecompte;
use AcMarche\Edr\Entity\Facture\FacturePresence;
use AcMarche\Edr\Entity\Facture\FactureReduction;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * @property array|Ecole[] $ecolesListing
 */
interface FactureInterface
{
    public const OBJECT_PRESENCE = 'presence';
    public const OBJECT_ACCUEIL = 'accueil';
    public const OBJECT_PLAINE = 'plaine';

    public function __toString(): string;

    public function getId(): ?int;

    public function getRue(): ?string;

    public function setRue(?string $rue): void;

    public function getCodePostal(): ?string;

    public function setCodePostal(?string $code_postal): void;

    public function getLocalite(): ?string;

    public function setLocalite(?string $localite): void;

    public function getCommunication(): ?string;

    public function setCommunication(string $communication): self;

    public function getEnfants(): array;

    public function getPayeLe(): ?\DateTimeInterface;

    public function setPayeLe(?\DateTimeInterface $payeLe): self;

    public function getFactureLe(): ?\DateTimeInterface;

    public function setFactureLe(\DateTimeInterface $factureLe): self;

    public function getEnvoyeLe(): ?\DateTimeInterface;

    public function setEnvoyeLe(?\DateTimeInterface $envoyeLe): self;

    public function getEnvoyeA(): ?string;

    public function setEnvoyeA(?string $envoyeA): self;

    public function getMois(): ?string;

    public function setMois(string $mois): self;

    public function getPlaineNom(): ?string;

    public function setPlaineNom(?string $plaine): self;

    public function getEcoles(): ?string;

    public function setEcoles(?string $ecoles): self;

    public function getMontantObsolete(): ?string;

    public function setMontantObsolete(?string $montant_obsolete): self;

    public function getClotureObsolete(): ?bool;

    public function setClotureObsolete(?bool $cloture_obsolete): self;

    /**
     * @return Collection|FactureComplement[]
     */
    public function getFactureComplements(): Collection;

    public function addFactureComplement(FactureComplement $factureComplement): self;

    public function removeFactureComplement(FactureComplement $factureComplement): self;

    /**
     * @return Collection|FactureDecompte[]
     */
    public function getFactureDecomptes(): Collection;

    public function addFactureDecompte(FactureDecompte $factureDecompte): self;

    public function removeFactureDecompte(FactureDecompte $factureDecompte): self;

    /**
     * @return Collection|FacturePresence[]
     */
    public function getFacturePresences(): Collection;

    public function addFacturePresence(FacturePresence $facturePresence): self;

    public function removeFacturePresence(FacturePresence $facturePresence): self;

    /**
     * @return Collection|FactureReduction[]
     */
    public function getFactureReductions(): Collection;

    public function addFactureReduction(FactureReduction $factureReduction): self;

    public function removeFactureReduction(FactureReduction $factureReduction): self;

    public function getNom(): ?string;

    public function setNom(string $nom): void;

    public function getPrenom(): ?string;

    public function setPrenom(string $prenom): void;

    public function getRemarque(): ?string;

    public function setRemarque(?string $remarque): void;

    public function getCreatedAt(): ?DateTimeInterface;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): void;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;

    public function updateTimestamps(): void;

    public function getTuteur(): ?Tuteur;

    public function setTuteur(?Tuteur $tuteur): void;

    public function getUserAdd(): ?string;

    public function setUserAdd(?string $userAdd): void;

    public function setUuid(UuidInterface $uuid): void;

    public function getUuid(): ?UuidInterface;

    public function generateUuid(): void;

    public function getPlaine(): ?Plaine;

    public function setPlaine(?Plaine $plaine): void;
}
