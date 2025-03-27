<?php

namespace App\Controller;

use App\Manager\DraftManager;
use App\Manager\PokemonManager;
use App\Model\Draft;

class HomeController{

    private PokemonManager $pokemonManager;
    private DraftManager $draftManager;

    public function __construct() {
        
        $this->pokemonManager = new PokemonManager();
        $this->draftManager = new DraftManager();
    
    }

    public function homePage()
    {
       $pokemons = $this->pokemonManager->selectAll();
       require_once("./templates/all_pokemons.php");
    }

    public function drafts()
    {
       $drafts = $this->draftManager->selectAll();
       require_once("./templates/drafts.php");
    }
}