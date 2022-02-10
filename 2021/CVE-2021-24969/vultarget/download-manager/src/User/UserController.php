<?php


namespace WPDM\User;


if(!defined("ABSPATH")) die("Shit happens!");

class UserController
{
    private static $instance;

    public $data;
    public $dashboard;
    public $login;
    public $register;
    public $profile;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->data             = User::getInstance();
        $this->login            = Login::getInstance();
        $this->register         = Register::getInstance();
        $this->dashboard        = Dashboard::getInstance();
        EditProfile::getInstance();
    }
}
