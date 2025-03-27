<?php
require_once("header.php");
?>
<h1>Liste des Pokémon</h1>
<div class="container">
    <?php foreach ($pokemons as $pokemon): ?>
        <div class="pokemon-card">
            <h2>#<?= $pokemon->getPokedexId() ?> - <?= $pokemon->getNameFr() ?></h2>
            <img class="pokemon-image" src="<?= $pokemon->getImage() ?>" alt="<?= $pokemon->getNameFr() ?>">
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