<?php

abstract class Controller {

    protected Model $model;

    public function __construct(Model $model) {

        $this->model = $model;
    }


}
