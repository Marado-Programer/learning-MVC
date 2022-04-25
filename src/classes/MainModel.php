<?php

/**
 *
 */

class MainModel
{
    public $form_data;
    public $form_msg;
    public $form_confirma;
    public $db;
    public $controller;
    public $parameters;
    public $userdata;

    protected $instancer;

    public function __construct($db = null, $controller = null)
    {
        $this->db = $db;
        $this->instancer = Instanceator::getInstanceator($db);
        $this->controller = $controller;
        $this->parameters = $this->controller->parameters;
    }

    public function inverte_data($data = null)
    {
        $nova_data = null;

        if ($data) {
            $data = preg_split('/\-|\/|\s|:/', $data);

            $data = array_map('trim', $data);

            $nova_data .= checkArray($data, 2) . '-';
            $nova_data .= checkArray($data, 1) . '-';
            $nova_data .= checkArray($data, 0);

            if (checkArray($data, 3))
                $nova_data .= ' ' . checkArray($data, 3);
            if (checkArray($data, 4))
                $nova_data .= ':' . checkArray($data, 4);
            if (checkArray($data, 5))
                $nova_data .= ':' . checkArray($data, 5);
        }

        return $nova_data;
    }
}

