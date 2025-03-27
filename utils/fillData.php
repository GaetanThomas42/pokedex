<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Manager\DatabaseManager;
use App\Manager\PokemonManager;

function fillDB(PDO $pdo): void
{
    $url = 'https://tyradex.app/api/v1/pokemon';
    $data = file_get_contents($url);
    $pokemonData = json_decode($data, true);

    try {
        $pdo = DatabaseManager::getConnexion();
        // Suppression des anciennes tables
        $pdo->exec("DROP TABLE IF EXISTS `Pokemon_Type_Relation`;");
        $pdo->exec("DROP TABLE IF EXISTS `Pokemon`;");
        $pdo->exec("DROP TABLE IF EXISTS `Pokemon_Type`;");
        // Création de la table `Pokemon` avec les statistiques intégrées
        $pdo->exec("CREATE TABLE `Pokemon` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `pokedexId` INT(11) NOT NULL,
            `nameFr` MEDIUMTEXT NOT NULL,
            `nameJp` MEDIUMTEXT NOT NULL,
            `generation` INT(11) NOT NULL,
            `category` MEDIUMTEXT NOT NULL,
            `image` MEDIUMTEXT NOT NULL,
            `imageShiny` MEDIUMTEXT NULL,
            `height` DOUBLE NOT NULL,
            `weight` DOUBLE NOT NULL,
            `hp` INT(11) NOT NULL,
            `atk` INT(11) NOT NULL,
            `def` INT(11) NOT NULL,
            `spe_atk` INT(11) NOT NULL,
            `spe_def` INT(11) NOT NULL,
            `vit` INT(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Création de la table `Pokemon_Type`
        $pdo->exec("CREATE TABLE `Pokemon_Type` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL UNIQUE,
            `image` MEDIUMTEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $pdo->exec("CREATE TABLE draft (
    id INT AUTO_INCREMENT PRIMARY KEY,
    selected_pokemon_id INT NOT NULL,
    status ENUM('en cours', 'terminé') DEFAULT 'en cours',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (selected_pokemon_id) REFERENCES pokemon(id));");
        // Création de la table relationnelle `Pokemon_Type_Relation`
        $pdo->exec("CREATE TABLE `Pokemon_Type_Relation` (
            `pokemon_id` INT(11) NOT NULL,
            `type_id` INT(11) NOT NULL,
            FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`type_id`) REFERENCES `Pokemon_Type`(`id`) ON DELETE CASCADE,
            PRIMARY KEY (`pokemon_id`, `type_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Préparation de l'insertion des Pokémon
        $stmtPokemon = $pdo->prepare("INSERT INTO Pokemon (
            pokedexId, generation, category, nameFr, nameJp, image, imageShiny, height, weight, 
            hp, atk, def, spe_atk, spe_def, vit
        ) VALUES (
            :pokedexId, :generationNumber, :category, :nameFr, :nameJp, :image, :imageShiny, :height, :weight, 
            :hp, :atk, :def, :spe_atk, :spe_def, :vit
        )");

        // Insertion des types
        $stmtType = $pdo->prepare("INSERT INTO Pokemon_Type (name, image) VALUES (:name, :image)");

        // Liaison Pokémon - Type
        $stmtRelation = $pdo->prepare("INSERT INTO Pokemon_Type_Relation (pokemon_id, type_id) VALUES (:pokemon_id, :type_id)");

        // Stockage des types déjà insérés
        $existingTypes = [];
        $queryTypeId = $pdo->prepare("SELECT id FROM Pokemon_Type WHERE name = :name");

        foreach ($pokemonData as $pokemon) {
            $params = [
                ':generationNumber' => $pokemon['generation'],
                ':pokedexId' => $pokemon['pokedex_id'],
                ':category' => $pokemon['category'],
                ':nameFr' => $pokemon['name']['fr'],
                ':nameJp' => $pokemon['name']['jp'],
                ':image' => $pokemon['sprites']['regular'],
                ':imageShiny' => $pokemon['sprites']['shiny'],
                ':height' => (float)$pokemon['height'],
                ':weight' => (float)$pokemon['weight'],
                ':hp' => $pokemon['stats']['hp'] ?? 0,
                ':atk' => $pokemon['stats']['atk'] ?? 0,
                ':def' => $pokemon['stats']['def'] ?? 0,
                ':spe_atk' => $pokemon['stats']['spe_atk'] ?? 0,
                ':spe_def' => $pokemon['stats']['spe_def'] ?? 0,
                ':vit' => $pokemon['stats']['vit'] ?? 0
            ];

            $stmtPokemon->execute($params);
            $pokemonId = $pdo->lastInsertId();

            // Insertion des types
            foreach ($pokemon['types'] as $type) {
                if (!isset($existingTypes[$type['name']])) {
                    $stmtType->execute([':name' => $type['name'], ':image' => $type['image']]);
                    $typeId = $pdo->lastInsertId();

                    if (!$typeId) {
                        $queryTypeId->execute([':name' => $type['name']]);
                        $typeId = $queryTypeId->fetchColumn();
                    }

                    $existingTypes[$type['name']] = $typeId;
                }

                $stmtRelation->execute([':pokemon_id' => $pokemonId, ':type_id' => $existingTypes[$type['name']]]);
            }
        }

        echo "Données insérées avec succès dans la base de données.";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
