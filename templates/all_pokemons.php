<?php
require_once("header.php");
?>
<h1>Liste des Pokémon</h1>
<div class="container d-flex justify-content-evenly flex-wrap">
    <?php foreach ($pokemons as $pokemon): ?>
        <div class="pokemon-card col-4">
            <h2>#<?= $pokemon->getPokedexId() ?> - <?= $pokemon->getNameFr() ?></h2>
            <div class="relative <? ?>" style="height: 200px">
                <img class="pokemon-image normal" src="<?= $pokemon->getImage() ?>" alt="<?= $pokemon->getNameFr() ?>">
                <img class="pokemon-image shiny" src="<?= $pokemon->getImageShiny() ?>" alt="<?= $pokemon->getNameFr() ?>">
            </div>

            <p>Catégorie : <?= $pokemon->getCategory() ?></p>
            <p>Génération : <?= $pokemon->getGeneration() ?></p>
            <div class="pokemon-types">
                <?php foreach ($pokemon->getTypes() as $type): ?>
                    <img src="<?= $type->getImage() ?>" alt="<?= $type->getName() ?>" title="<?= $type->getName() ?>">
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once("footer.php");
?>