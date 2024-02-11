<?php

namespace app\database\entity;

class PostEntity extends Entity {

    public function save() {
        return $this->attributes;
    }

}