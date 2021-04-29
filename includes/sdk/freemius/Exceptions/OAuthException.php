<?php
namespace WFLR;

    class Freemius_OAuthException extends Freemius_Exception
    {
        public function __construct($pResult)
        {
            parent::__construct($pResult);
        }
    }