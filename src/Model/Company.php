<?php

namespace App\Model;

class Company {

    public function __construct(
        private string $siren,
        private string $siret,
        private string $nom_raison_sociale,
        private string $adresse
    ) {}

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

    public function setNomRaisonSociale(string $nom_raison_sociale): Company {
        $this->nom_raison_sociale = $nom_raison_sociale;
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
        return new Company(
            $object->siren,
            $object->siege->siret,
            $object->nom_raison_sociale,
            $object->siege->adresse,
        );
    }

    public static function toCompanies(array $array): array {
        $companies = [];
        foreach ($array as $object) {
            $companies[] = new Company(
                $object->siren,
                $object->siege->siret,
                $object->nom_raison_sociale,
                $object->siege->adresse,
            );
        }

        return $companies;
    }
}