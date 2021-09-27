<?php

 
    
// Можно использовать анонимные функции
spl_autoload_register(function ($class) {
    require_once __DIR__.'\\'.'Classes\\'.$class.'.php';
});


if($argv[1] ==='php')
{
    $develop = new PhpDeveloperCreator();
}else if($argv[1]==='c++')
{
    $develop = new CppDeveloperCreator();
}else
{
    $develop = new PhpDeveloperCreator();
}

 
 $cppDeveloper = $develop->create();
 $cppDeveloper->writeCode();

