# DParser
Simple Dice Roller + Calculator for Laravel 5.5

## Installation
Simple require package in composer
```
composer require jes490/dparser
```

DParser uses Laravel 5.5 Package Auto-Discovery, so you don't have to add ServiceProvider to providers array.

#### Without Auto-Discovery
If you don't use Package Auto-Discovery, simply add next provider to providers array in config/app.php:
```
Jes490\DParser\DParserServiceProvider::class,
```

And if you want use Facade, add this to your aliases array in config/app.php:
```
'DParser' => Jes490\DParser\Facade\DParser::class,
```

## Usage

You can now use DParser as Facade in your app. 
```php
DParser::roll($expression);

//You can assess result of your expression like this:
DParser::roll($expression)->getResult();
//Or just use plain auto-conversion __toString()
DParser::roll('2+2'); //returns '4';

//You can access individual rolls like this:
DParser::roll('2d6')->getRolls(); //returns array of rolls results
```

## Functionallity

Currently DParser supports next list of operators: '-', '+', '*', '/', 'd'. 
You can build complex expression like these: 
```php
DParser::roll('2d6+1d8+1d12+20');
DParser::roll('2+3*2-1+2d6-3*4');
```
