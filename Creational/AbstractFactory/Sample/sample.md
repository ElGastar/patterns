## Шаблон фабричный метод (Factory Method)

### Цель

Сзодания интерфейса, который создает обект. При этом, выбор того, экемпляр какого класса создовать 
остается за классами, который имплементируют данный интерфейс.

### Для чего используется  

Для делигирования создания классов, другому (дочерным ) классу 

### Пример использование 


* Заранее неизвестно, экземпляры, кокого класса нужно будет создовать.
* класс спроектировани таким образом, что создаваемые им обекты имеют свойства определенного класса 

### Пример 

Представим, что создаем прогурамму каторая выводит информацию о программистах 

```php
<?php

class Program {

 $programmerCpp = new CppDeveloper();

$programmerCpp.writeCppCode();

}

class CppDeveloper {

public function writeCppCode():void{
    echo "Writing C++ code";
}

}

```

В дальнейшем наша программа расширяется и нам нужно добавить PHP программиста 

```php
<?php

class Program {

 $programmerPhp = new PhpDeveloper();

$programmerPhp.writePhpCode();

}

class PhpDeveloper {

public function writePhpCode():void{
    echo "Writing Php code";
}

}

```
 если нам нужно поменять программиста, нужно в коде клиента делать много лишних действии ,если программа большая это муторное дело 

Давайте удалим эти два класса и добавим интерфейс Developer, в котором будет только один метод 

```php

interface Developer {

    public function writeCode();

}

```
и создадим два класса, кторый имплеминтруют этот интерфейс 

```php

class PhpDeveloper implements Developer {

public function writeCode():void{
    echo "Writing PHP code";
}

}

class CppDeveloper implements Developer {

public function writeCode():void{
    echo "Writing C++ code";
}

}

```
После того, как эти два класа имплеминтируют один интерфейс, в клиентском коде може взаимозаменить эти классы  

 ```php
<?php

class Program {

$programmerPhp = new PhpDeveloper();

$programmerPhp.writePhpCode();

}
```
теперь создадим фабричные методы для создания классов. 

```php

abstract class DeveloperCreator{
    public function create():Developer;
}

class PhpDeveloperCreator extends DeveloperCreator{

    public function create():Developer{
        return new PhpDeveloper();
    };
}

class CppDeveloperCreator extends DeveloperCreator{

    public function create():Developer{
        return new CppDeveloper();
    };
}

```

Теперь в клиентском коде можно создать оба класса с помощю фабричного метода 


```php
<?php

class Program {

$developerCreator = new PhpDeveloperCreator();//Вызываем класс

$developer = $programmer.create();//создаем обект 

$developer.writePhpCode();


}
```

Что бы облегчить работу ,можно создать статическую функцию ,которая возвращает нужный класс 

```php
<?php

class Program {

$developerCreator = new PhpDeveloperCreator();//Вызываем класс

$developer = $programmer.create();//создаем обект 

$developer.writePhpCode();

  static function createDeveloperBySpeciality(String $speciality): DeveloperCreator{
      if($speciality==="php"){
          return new PhpDeveloperCreator();
      }else if($speciality==="cpp"){
          return new CppDeveloperCreator();
      }else {
          throw new Exception($speciality+"is unkown");
      }

  }


}
```