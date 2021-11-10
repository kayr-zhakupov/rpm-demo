#
Самопальный нативный мини-движок, типичный MVC, структура проекта и идеология вдохновлены Laravel.
Реализован кастомный автозагрузчик классов - boot/autoloader.php.
Требуемая версия PHP - 7.4, поскольку:
* предпочитаю использовать типизированные поля классов (7.4+)
* nullable-типы в сигнатурах функций и методов (7.1+)
* скалярные типы в сигнатурах функций и методов (7.0+)
* типы значений, возвращаемых методами и функциями (7.0+)
* coalesce-оператор "??" (удобно для доступа к полям массивов и объектов без проверки наличия соответствующих ключей) (7.0+)

Точка входа - public/index.php

Код загрузки приложения - boot/app.php

Контроллер - callable-переменная: функция либо массив типа [object, string]; в данном проекте не принимает аргументов
Если контроллер не определён (переменная равна null) - выводится 404
Результаты запуска определённого контроллера:
* массив - возврат json-строки
* скалярная величина - возврат как есть
* null и прочее - ответ 200, пустой вывод

Таблицу в MySQL можно создать запуском скрипта scripts/db-migrate.php.

Манера написания sql-запросов через implode() частично вдохновлена механизмом dbDelta() из WordPress.

# Разворот проекта

* создать файл /env/env.php; содержимое взять из /env.example.php, заполнить нужными значениями
* убедиться в наличии папки /log
* (опционально) создать пустой файл /public/favicon.ico