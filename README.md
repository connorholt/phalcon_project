# Поиск местопололожения по ip

### Используемые технологии:
1. Php framework phalcon
2. Postgres
3. Расширение postgres ip4r (https://github.com/RhodiumToad/ip4r)
4. База ip адресов ip2location http://lite.ip2location.com/database/ip-country-region-city-latitude-longitude-zipcode-timezone

### Установка проекта:
1. Поставить phalcon, postgres + ip4r
2. Положить файлы с ip адресами в папку /public/files, два файла для ipv4 и для ipv6
3. Запустить миграции: phalcon migration run
4. Запустить консольный скрипт, которые с csv перенес все ip адреса в нужном формате в базу: php app/cli.php copy-data
5. Проверить поиск можно по url /ipv4/get/{ip}, где {ip} адрес в формате x.x.x.x, результат будет json с координатами города

### Результат:
1. Поиск в базе среди ~4000000 ip адресов (v4), по id занимает в среднем 0,02 ms
2. Поиск в базе среди ~4000000 ip адресов (v4), по интервалу ip занимает в среднем 0,07 ms

### @todo
1. Рефакторинг консольного скрипта
2. Сделать поиск по ipv6 и копирование ipv6 адресов
