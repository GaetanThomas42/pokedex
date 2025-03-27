<?php

namespace App\Model;

class Pokemon
{

    public function __construct(
        private ?int $id,
        private int $pokedexId,
        private string $nameFr,
        private string $nameJp,
        private int $generation,
        private string $category,
        private string $image,
        private ?string $imageShiny,
        private float $height,
        private float $weight,
        private array $types = [],
    ) {
        $this->id = $id;
        $this->pokedexId = $pokedexId;
        $this->nameFr = $nameFr;
        $this->nameJp = $nameJp;
        $this->generation = $generation;
        $this->category = $category;
        $this->image = $image;
        $this->imageShiny = $imageShiny;
        $this->height = $height;
        $this->weight = $weight;
        $this->types = $types;
    }

    public function addType(PokemonType $type)
    {
        $this->types[] = $type;
    }

    public function removeType(PokemonType $type)
    {

        $key = array_search($type, $this->types);
        if ($key !== false) {
            unset($this->types[$key]);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokedexId()
    {
        return $this->pokedexId;
    }


    public function setPokedexId($pokedexId)
    {
        $this->pokedexId = $pokedexId;
    }

    /**
     * Get the value of nameFr
     */
    public function getNameFr()
    {
        return $this->nameFr;
    }

    public function setNameFr($nameFr)
    {
        $this->nameFr = $nameFr;
    }

    public function getNameJp()
    {
        return $this->nameJp;
    }

    public function setNameJp($nameJp)
    {
        $this->nameJp = $nameJp;
    }

    /**
     * Get the value of generation
     */
    public function getGeneration()
    {
        return $this->generation;
    }


    public function setGeneration($generation)
    {
        $this->generation = $generation;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImageShiny()
    {
        return $this->imageShiny;
    }

    public function setImageShiny($imageShiny)
    {
        $this->imageShiny = $imageShiny;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function setTypes($types)
    {
        $this->types = $types;
    }
}
