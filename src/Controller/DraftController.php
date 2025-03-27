<?php

namespace App\Controller;

use App\Manager\DraftEliminationsManager;
use App\Manager\PokemonManager;
use App\Manager\DraftManager;
use App\Model\Draft;

class DraftController
{
    /**
     * @var PokemonManager Gestionnaire des Pokémon
     */
    private PokemonManager $pokemonManager;

    /**
     * @var DraftManager Gestionnaire des drafts
     */
    private DraftManager $draftManager;

    /**
     * Constructeur de DraftController
     */
    public function __construct()
    {
        // Initialisation des gestionnaires pour interagir avec la base de données
        $this->pokemonManager = new PokemonManager();
        $this->draftManager = new DraftManager();
    }

    /**
     * Démarre une nouvelle draft en sélectionnant un Pokémon aléatoire
     * et en redirigeant vers l'étape de sélection.
     *
     * @return void
     */
    public function startDraft(): void
    {
        // Sélectionne un Pokémon aléatoire
        $randomPokemon = $this->pokemonManager->selectRandomPokemon();

        // Crée un nouvel objet Draft avec ce Pokémon
        $draft = new Draft(null, $randomPokemon, "en cours", []);

        // Insère la draft en BDD et récupère son ID
        $draftId = $this->draftManager->insert($draft);
        
        // Redirige vers l'écran de sélection du Pokémon
        header("Location: index.php?action=pick&id=" . $draftId);
        exit();
    }

    /**
     * Affiche l'écran où l'utilisateur choisit un Pokémon dans la draft en cours.
     *
     * @param int $draftId ID de la draft en cours
     * @return void
     */
    public function pickPokemon(int $draftId): void
    {
        // Récupère la draft à partir de son ID
        $draft = $this->draftManager->selectById($draftId);

        // Récupère le Pokémon actuel de la draft
        $currentPokemon = $draft->getPokemon();
        $randomPokemon = null;

        // Récupère les Pokémon déjà éliminés et ajoute celui actuellement sélectionné
        $eliminationIds = $draft->getEliminatedPokemons();
        $eliminationIds[] = $currentPokemon->getId();

        if(count($eliminationIds) > 1005){
            $draft->setStatus("finito");
            $this->draftManager->update($draft);
            header("Location: index.php?action=drafts");
        }
        // Sélectionne un Pokémon aléatoire qui n'a pas encore été éliminé
        do {
            $randomPokemon = $this->pokemonManager->selectRandomPokemon();
        } while (in_array($randomPokemon->getId(), $eliminationIds));
        
        //dump($draft,"Current",$currentPokemon,"Eliminés",$eliminationIds,"Random",$randomPokemon);

        // Charge la vue pour afficher le choix du Pokémon
        require_once('./templates/pick_pokemon.php');
    }

    /**
     * Gère la confirmation du choix d'un Pokémon dans la draft.
     *
     * @param int $draftId ID de la draft
     * @return void
     */
    public function confirmPick(int $draftId): void
    {
        //dd($_POST);
        // Vérifie si la requête est bien un formulaire POST et que les données sont présentes
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pokemon_id'], $_POST['currentPokemonId'], $_POST['randomPokemonId'])) {
          
            // Récupère la draft associée à l'ID
            $draft = $this->draftManager->selectById($draftId);

            // Récupère l'ID du Pokémon sélectionné par le joueur et convertit en INT
            $selectedPokemonId = (int)$_POST['pokemon_id'];

            // Récupère l'ID des Pokémon en jeu
            $draftPokemonId = (int)$_POST['currentPokemonId'];
            $randomPokemon = (int)$_POST['randomPokemonId'];

            $draftEliminationsManager = new DraftEliminationsManager();
            // Détermine quel Pokémon doit être éliminé
            $pokemonEliminated = ($randomPokemon === $selectedPokemonId) ? $draftPokemonId : $randomPokemon;
            $draftEliminationsManager->insert($draft->getId(), $pokemonEliminated);

            // Met à jour la draft avec le Pokémon sélectionné
            $draft->setPokemon($this->pokemonManager->selectById($selectedPokemonId));
            $this->draftManager->update($draft);

            //dd($draft,"Eliminé $pokemonEliminated","Selectionné $selectedPokemonId");

            // Redirige vers la prochaine étape du draft
            header("Location: index.php?action=pick&id=" . $draft->getId());
            exit();
        }

        // Redirige vers l'accueil tout autre cas
        header("Location: index.php");
        exit();
    }

}
