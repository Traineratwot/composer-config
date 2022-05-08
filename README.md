# composer-config

## en

This is a plugin for managing the settings of your libraries and projects

How does it work?

- install plugins or a plugin-dependent package
- if this is a project
	- in the `composer.json` file, you need to set the path to the php file with your future settings in `extra.composer-config.configPath`
	- in the settings file, you must register all the global settings of your application in this way `Config::set('name', 'value','namespace');`
- if library
	- in the `composer.json` file, you can add the settings keys and their description to the `extra.composer-config`
	- in the `composer.json` file, you can set the desired package name to `extra.composer-config.namespace` by default package name
	- in your project, use `Config::get('name','namespace');`

## ru

Это плагин для управления настройками ваших библиотек и проектов

Как это работает?

- установить плагины или пакет зависящий от плагина
- если это проект
	- в файле `composer.json` надо установить в `extra.composer-config.configPath` путь до php файла с вашими будущими настройками
	- в файле с настрйками вы должны прописать все глобальный насройки вашего приложения таким образом `Config::set('name', 'value','namespace');`
- если библиотека
	- в файле `composer.json` можно добавить в `extra.composer-config` ключи настроек и их описание
	- в файле `composer.json` можно установить в `extra.composer-config.namespace` желаемый `namespace` по умолчанию имя пакета
	- в вашем проекте используйте `Config::get('name','namespace');`

```json
{
	"type" :"project",
	"extra":{
		"composer-config":{
			"configPath":"src/config.php"
		}
	}
}

```

```json
{
	"type" :"library",
	"extra":{
		"composer-config":{
			"namespace":"lb1",
			"required" :{
				"test1":"test1 description"
			},
			"optional" :{
				"test2":"test2 description"
			}
		}
	}
}

```

### config.php

```php
    use Traineratwot\cc\Config;

	Config::set('test1', 'value1');
	Config::set('test1', 'value2','lb1'); 
	Config::set('test2', 'value3');
	
	Config::get('test1')//value1
	Config::get('test1','lb1')//value2
	Config::get('test2')//value3
	Config::get('test2','lb1')//value3

```

### commands
>**getAllConfigs**
> 
> en
> 
> Returns all settings that can or should be defined in the project
> 
> ru
> 
> Возвращякт все настройки которые можно или нужну определить в проэкте
>
>```bash
>composer getAllConfigs
>
>```
>
>```txt
> ------------ ----------- ------------------ ---------- --------- 
>  config key   namespace   description        type       is set?  
> ------------ ----------- ------------------ ---------- ---------
> string       string      string             string     yes/no
> ------------ ----------- ------------------ ---------- ---------
>
>```