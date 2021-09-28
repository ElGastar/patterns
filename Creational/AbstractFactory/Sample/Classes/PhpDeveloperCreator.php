<?php

class PhpDeveloperCreator implements IDeveloperCreator{
    

    public function create():AbstractDeveloper{
            return new PhpDeveloper();
    }
}