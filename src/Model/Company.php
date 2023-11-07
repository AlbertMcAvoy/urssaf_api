<?php

namespace App\Model;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Company {

    #[Assert\NotBlank]
    private string $siren;

    private string $siret = "";

    #[Assert\NotBlank]
    private string $nom_raison_sociale;

    #[Assert\NotBlank]
    private string $adresse;

    public function getSiren(): string {
        return $this->siren;
    }

    public function setSiren(string $siren): Company {
        $this->siren = $siren;
        return $this;
    }

    public function getSiret(): string {
        return $this->siret;
    }

    public function setSiret(string $siret): Company {
        $this->siret = $siret;
        return $this;
    }

    public function getNomRaisonSociale(): string {
        return $this->nom_raison_sociale;
    }

    public function setNomRaisonSociale(string $nomRaisonSociale): Company {
        $this->nom_raison_sociale = $nomRaisonSociale;
        return $this;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): Company {
        $this->adresse = $adresse;
        return $this;
    }

    public static function toCompany(object $object): Company {

        $company = new Company();
        return $company->setSiren($object->siren)
            ->setSiret(property_exists($object, 'siege') ? $object->siege->siret : $object->siret)
            ->setNomRaisonSociale( property_exists($object, 'nom_raison_sociale') ? $object->nom_raison_sociale : $object->nomRaisonSociale)
            ->setAdresse(property_exists($object, 'siege') ? $object->siege->adresse : $object->adresse);
    }

    public static function toCompanies(array $array): array {
        $companies = [];
        foreach ($array as $object) {
            $company = new Company();
            $companies[] = $company->setSiren($object->siren)
                ->setSiret(property_exists($object, 'siege') ? $object->siege->siret : $object->siret)
                ->setNomRaisonSociale( property_exists($object, 'nom_raison_sociale') ? $object->nom_raison_sociale : $object->nomRaisonSociale)
                ->setAdresse(property_exists($object, 'siege') ? $object->siege->adresse : $object->adresse);
        }

        return $companies;
    }
}