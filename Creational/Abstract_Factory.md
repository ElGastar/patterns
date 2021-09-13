#### Абстрактная фабрика — это порождающий паттерн проектирования, который решает проблему создания целых 
#### семейств связанных продуктов, без указания конкретных классов продуктов.

Абстрактная фабрика задаёт интерфейс создания всех доступных типов продуктов, а каждая конкретная 
реализация фабрики порождает продукты одной из вариаций. Клиентский код вызывает методы фабрики 
для получения продуктов,вместо самостоятельного создания с помощью оператора new. При этом фабрика 
сама следит за тем, чтобы создать продукт нужной вариации.

### Пример из реальной жизни
Интерфейс Абстрактной фабрики объявляет создающие методы для каждого определённого типа продукта.

В этом примере паттерн Абстрактная фабрика предоставляет инфраструктуру для создания нескольких разновидностей шаблонов для одних и тех же элементов веб-страницы.

Чтобы веб-приложение могло поддерживать сразу несколько разных движков рендеринга страниц, его классы должны работать с шаблонами только через интерфейсы, не привязываясь к конкретным классам. Чтобы этого достичь, объекты приложения не должны создавать шаблоны напрямую, а поручать создание специальным объектам-фабрикам, с которыми тоже надо работать через абстрактный интерфейс.

Благодаря этому, вы можете подать в приложение фабрику, соответствующую одному из движков рендеринга, зная, что с этого момента, все шаблоны будут порождаться именно этой фабрикой, и будут соответствовать движку рендеринга этой фабрики. Если вы захотите сменить движок рендеринга, то всё что нужно будет сделать — это подать в приложение объект фабрики другого типа и ничего при этом не сломается.

```php
<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;

    public function getRenderer(): TemplateRenderer;
}
```


Каждая Конкретная Фабрика соответствует определённому варианту (илисемейству) продуктов.
Эта Конкретная Фабрика создает шаблоны Twig.
 
 ```php
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }
}
```

А эта Конкретная Фабрика создает шаблоны PHPTemplate.

```php
class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new PHPTemplateRenderer();
    }
}
```


Каждый отдельный тип продукта должен иметь отдельный интерфейс. Все варианты продукта должны соответствовать одному интерфейсу.
Например, этот интерфейс Абстрактного Продукта описывает поведение шаблонов заголовков страниц.
 
 ```php
interface TitleTemplate
{
    public function getTemplateString(): string;
}
```


Этот Конкретный Продукт предоставляет шаблоны заголовков страниц Twig.
 
```php
class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}
```


А этот Конкретный Продукт предоставляет шаблоны заголовков страниц PHPTemplate.
 
```php
class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= \$title; ?></h1>";
    }
}
```


Это еще один тип Абстрактного Продукта, который описывает шаблоны целых страниц.
 
 ```php
interface PageTemplate
{
    public function getTemplateString(): string;
}
```


Шаблон страниц использует под-шаблон заголовков, поэтому мы должны предоставить способ установить объект для этого под-шаблона. Абстрактная
фабрика позаботится о том, чтобы подать сюда под-шаблон подходящего типа.
 
  ```php
abstract class BasePageTemplate implements PageTemplate
{
    protected $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}
```


Вариант шаблонов страниц Twig.

 ```php
class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content">{{ content }}</article>
        </div>
        HTML;
    }
}
```

Вариант шаблонов страниц PHPTemplate.

```php
class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content"><?= \$content; ?></article>
        </div>
        HTML;
    }
}
```


Классы отрисовки отвечают за преобразовании строк шаблонов в конечный HTML
код. Каждый такой класс устроен по-раному и ожидает на входе шаблоны только
своего типа. Работа с шаблонами через фабрику позволяет вам избавиться от
риска подать в отрисовщик шаблон не того типа.

```php
interface TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string;
}
```


Отрисовщик шаблонов Twig.

```php
class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        return \Twig::render($templateString, $arguments);
    }
}
```

 Отрисовщик шаблонов PHPTemplate. Оговорюсь, что эта реализация очень простая,
 если не примитивная. В реальных проектах используйте `eval` с
 осмотрительностью, т.к. неправильное использование этой функции может
 привести к дырам безопасности.
 
```php
 class PHPTemplateRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
```

Клиентский код. Обратите внимание, что он принимает класс Абстрактной Фабрики
в качестве параметра, что позволяет клиенту работать с любым типом конкретной
фабрики.

```php
class Page
{

    public $title;

    public $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
```
Вот как вы бы использовали этот шаблон в дальнейшем. Обратите внимание,
что класс страницы не зависит ни от классов шаблонов, ни от классов
отрисовки.

```php
    public function render(TemplateFactory $factory): string
    {
        $pageTemplate = $factory->createPageTemplate();

        $renderer = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title' => $this->title,
            'content' => $this->content
        ]);
    }
}
```


Теперь в других частях приложения клиентский код может принимать фабричные объекты любого типа.

```php
$page = new Page('Sample page', 'This it the body.');

echo "Testing actual rendering with the PHPTemplate factory:\n";
echo $page->render(new PHPTemplateFactory());

//Можете убрать комментарии, если у вас установлен Twig.

//echo "Testing rendering with the Twig factory:\n"; echo $page->render(new
//TwigTemplateFactory());
```
