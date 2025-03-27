<?php
require_once("header.php");
?>

<style>
    .flip-card {
        width: 100%;
        height: 300px; /* Hauteur fixe pour s'aligner avec l'autre carte */
        perspective: 1000px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .flip-card-inner {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 1s;
    }

    .flip-card.flip .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-front, .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .flip-card-front {
        background-image: url('uploads/card.jpg'); 
        background-size: cover;
        background-position: center;
    }

    .flip-card-back {
        background: white;
        transform: rotateY(180deg);
        padding: 10px;
    }

    /* Ajustement pour aligner la taille des cartes */
    .pokemon-card {
        height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .pokemon-card img {
        max-height: 150px;
    }
</style>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h1 class="text-center text-primary">Sélectionnez votre Pokémon - Round: <?= (count($draft->getEliminatedPokemons()) + 1); ?></h1>

        <div class="row mt-4">
            <!-- Pokémon Actuel -->
            <div class="col-md-6 text-center">
                <div class="card p-3 pokemon-card">
                    <h4 class="text-warning">Pokémon actuel</h4>
                    <img src="<?= $currentPokemon->getImage(); ?>" class="img-fluid rounded" alt="<?= $currentPokemon->getNameFR(); ?>">
                    <h5 class="mt-3"><?= $currentPokemon->getNameFR(); ?></h5>
                </div>
            </div>

            <!-- Pokémon Proposé (Carte qui se retourne) -->
            <div class="col-md-6 text-center">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <!-- Dos de la carte -->
                        <div class="flip-card-front"></div>
                        <!-- Face de la carte -->
                        <div class="flip-card-back card p-3 pokemon-card">
                            <h4 class="text-primary">Pokémon proposé</h4>
                            <img src="<?= $randomPokemon->getImage(); ?>" class="img-fluid rounded" alt="<?= $randomPokemon->getNameFR(); ?>">
                            <h5 class="mt-3"><?= $randomPokemon->getNameFR(); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de sélection -->
        <form method="POST" action="index.php?action=confirm_pick&id=<?= $draftId; ?>" class="mt-4">
            <input type="hidden" name="currentPokemonId" value="<?= $currentPokemon->getId(); ?>">
            <input type="hidden" name="randomPokemonId" value="<?= $randomPokemon->getId(); ?>">

            <div class="d-flex justify-content-around mt-3">
                <button type="submit" name="pokemon_id" value="<?= $currentPokemon->getId(); ?>" class="btn btn-warning btn-lg">
                    Garder <?= $currentPokemon->getNameFR(); ?>
                </button>
                <button id="btnPK" type="submit" name="pokemon_id" value="<?= $randomPokemon->getId(); ?>" class="btn btn-primary btn-lg opacity-0">
                    Choisir <?= $randomPokemon->getNameFR(); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Déclencher le retournement après 2 secondes
    setTimeout(() => {
        document.querySelector(".flip-card").classList.add("flip");
        document.querySelector("#btnPK").classList.remove("opacity-0");

}, 2000);
</script>

<?php
require_once("footer.php");
?>
