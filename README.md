# composer-config

## en
This is a plugin for managing the settings of your libraries and projects

How does it work?
 
 - install plugins or a plugin-dependent package
 - if this is a project 
   - in the `composer.json` file, you need to set the path to the php file with your future settings in `extra.cc.configPath`
   - in the settings file, you must register all the global settings of your application in this way `Config::set('name', 'value','namespace');`

## ru
Это плагин для управления настройками ваших библиотек и проектов

Как это работает?

 - установить плагины или пакет зависящий от плагина
 - если это проект 
   - в файле `composer.json` надо установить в `extra.сc.configPath` путь до php файла с вашими будущими настрйками
   - в файле с настрйками вы должны прописать все глобальный насройки вашего приложения таким образом `Config::set('name', 'value','namespace');`