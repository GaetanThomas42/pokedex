<?php

namespace App\Model;

class PokemonType {
    public int $id;
    public string $name;
    public string $image;

    public function __construct(int $id, string $name, string $image) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of image
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}