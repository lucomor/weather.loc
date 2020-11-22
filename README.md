# weather app

1. Настройте подключение к БД в файле .ENV
2. Выполните команды для создания БД и таблиц

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

3. Укажите ключ API OpenWeatherMap а так же Координаты (lat, lon) города в

`src/Service/Weather.php getOpenWeatherMap`
