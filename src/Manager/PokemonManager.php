<?php

namespace App\Manager;

use App\Model\Pokemon;
use App\Model\PokemonType;
use PDO;

class PokemonManager extends DatabaseManager
{

    public function selectAll(): array
    {
        $reponse = self::getConnexion()->query($this->getBaseQuery());
        $arrayPokemons = $reponse->fetchAll();

        $objectPokemons = [];

        foreach ($arrayPokemons as $arrayPokemon) {
            $objectPokemons[] = $this->arrayToObject($arrayPokemon);
        }

        return $objectPokemons;
    }

    public function selectById(int $id): Pokemon|false
    {
        $reponse = self::getConnexion()->prepare($this->getBaseQuery() . " HAVING p.id = :id");
        $reponse->execute([
            ":id" => intval($id)
        ]);
        $arrayPokemon = $reponse->fetch();

        if (!$arrayPokemon) {
            return false;
        }

        return $this->arrayToObject($arrayPokemon);
    }

    public function selectByPokedexIdOrName(string|int $value): Pokemon|false
    {
        $query = $this->getBaseQuery() . ' HAVING p.pokedexId = :value OR p.nameFr = :value';
        $reponse = self::getConnexion()->prepare($query);
        $reponse->execute(['value' => $value]);
        $arrayPokemon = $reponse->fetch();

        if (!$arrayPokemon) {
            return false;
        }

        return $this->arrayToObject($arrayPokemon);
    }


    public function selectRandomPokemon(): Pokemon
    {
        $query = $this->getBaseQuery() . ' ORDER BY RAND() LIMIT 1';
        $reponse = self::getConnexion()->prepare($query);
        $reponse->execute();
        $arrayPokemon = $reponse->fetch();

        return $this->arrayToObject($arrayPokemon);
    }

    private function getBaseQuery(): string
    {
        return "SELECT p.id, p.pokedexId, p.nameFr, p.nameJp, p.generation, p.category, p.image, p.imageShiny, p.height, p.weight,
        GROUP_CONCAT(pt.id ORDER BY pt.id SEPARATOR ',') AS type_ids,
        GROUP_CONCAT(pt.name ORDER BY pt.id SEPARATOR ',') AS type_names,
         GROUP_CONCAT(pt.image ORDER BY pt.id SEPARATOR ',') AS type_images, 
         p.hp,p.atk,p.def,p.spe_atk,p.spe_def,p.vit FROM pokemon p LEFT JOIN pokemon_type_relation ptr ON p.id = ptr.pokemon_id LEFT JOIN pokemon_type pt ON ptr.type_id = pt.id GROUP BY p.id";
    }

    private function arrayToObject(array $data): Pokemon
    {
        $pokemon = new Pokemon(
            $data["id"],
            $data["pokedexId"],
            $data["nameFr"],
            $data["nameJp"],
            $data["generation"],
            $data["category"],
            $data["image"],
            $data["imageShiny"],
            $data["height"],
            $data["weight"],
            [],
            $data["hp"],
            $data["atk"],
            $data["def"],
            $data["spe_atk"],
            $data["spe_def"],
            $data["vit"]
        );

        if (!empty($data["type_ids"])) {
            $typeIds = explode(",", $data["type_ids"]);
            $typeNames = explode(",", $data["type_names"]);
            $typeImages = explode(",", $data["type_images"]);

            foreach ($typeIds as $index => $typeId) {
                $type = new PokemonType($typeId, $typeNames[$index], $typeImages[$index]);
                $pokemon->addType($type);
            }
        }
        return $pokemon;
    }
}
