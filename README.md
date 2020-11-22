# weather app

1. Клонируйте данный репозиторий
2. В папке репозитория выполните

`composer install`

3. Настройте подключение к БД в файле .ENV
4. Выполните команды для создания БД и таблиц

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

5. Укажите ключ API OpenWeatherMap а так же Координаты (lat, lon) города (defalut Kaliningrad) в

`src/Service/Weather.php getOpenWeatherMap`

6. Настройте cron на выполнение команды

`php bin/console cron:weather`
