# Введение

Самопальный нативный мини-движок, типичный MVC, структура проекта и идеология вдохновлены Laravel.
Реализован кастомный автозагрузчик классов - boot/autoloader.php.
Требуемая версия PHP - 7.4, поскольку:
* предпочитаю использовать типизированные поля классов (7.4+)
* nullable-типы в сигнатурах функций и методов (7.1+)
* скалярные типы в сигнатурах функций и методов (7.0+)
* типы значений, возвращаемых методами и функциями (7.0+)
* coalesce-оператор "??" (удобно для доступа к полям массивов и объектов без проверки наличия соответствующих ключей) (7.0+)
* интерфейс \Throwable (7.0+)

Точка входа - public/index.php

Код загрузки приложения - boot/app.php

Контроллер - callable-переменная: функция либо массив типа [object, string].
В случае массива можно передавать дополнительные аргументы: [object, string, ...args]
Если контроллер не определён (переменная равна null) - выводится 404
Результаты запуска определённого контроллера:
* массив - возврат json-строки
* скалярная величина - возврат как есть
* null и прочее - ответ 200, пустой вывод

Таблицу в MySQL можно создать запуском скрипта scripts/db-migrate.php.

Манера написания sql-запросов через implode() частично вдохновлена механизмом dbDelta() из WordPress.

# Разворот проекта

* git clone https://github.com/kayr-zhakupov/rpm-demo.git
* создать пустую базу данных mysql
* создать приложение в консоли разработчика ВКонтакте (тип: Сайт; адрес сайта: https://localhost:8001; базовый домен: localhost)
* запустить скрипт /scripts/jumpstart.php
* заполнить файл /env/env.php нужными значениями (vk_client_id - ID приложения; vk_client_secret - защищённый ключ)
* запустить скрипт /scripts/db-migrate.php
* `php -S localhost:8001 -t public` (из корневой папки проекта)

# Что можно было ещё добавить и оптимизировать

* 