<?php

class CppDeveloperCreator implements IDeveloperCreator{
    
    public function create():AbstractDeveloper{
        return new CppDeveloper();
    }
}