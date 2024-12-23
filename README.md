## Тестовое в Roistat

Тестовое задание для компании Roistat

### Задача:

Создать страницу и добавить на неё форму из 4-х полей: имя, email, телефон, цена.
Заявку из формы сайта создавать в AmoCRM, как сделку с прикрепленным к ней контактом. В контакт передавать имя, email и телефон. В сделку передавать цену.
Если пользователь провел на сайте больше 30 секунд - при создании сделки нужно передать эту информацию в AmoCRM в дополнительное поле в виде boolean значения (1 и 0 тоже подойдет), предварительно доп поле нужно создать в AmoCRM, название поля можно выбрать любое.
Пользователю который заполняет форму, не нужно проходить регистрацию ни в AmoCRM, ни где-либо еще.

FAQ:
Документация по API AmoCRM - https://developers.amocrm.ru/rest_api/
Код нужно выложить на https://github.com/, в идеале показать умение работать с git.

Для развертывания формы можно использовать как готовые хостинги, так и работать локально, например в докере.
Разрешается как использовать готовые библиотеки для взаимодействия с апи AmoCRM, так и самостоятельно реализовать это в необходимом виде.
Аккаунт в AmoCRM можно использовать любой, как и приложение для доступа к апи.
Для front-end и back-end нет ограничений по используемым технологиям, фреймворкам и прочему. Предпочтительный язык для back-end - PHP.

### Дедлайн

Сроки выполнения - 3 рабочих дня.

## Что нужно реализовать

### Фронт:

HTML страница с формой, 5 полей, 4 видимых и одно скрытое.

JavaScript код для валидации данных и отправку их на бэк.

### Бэк:

Скрипт с валидацией входящих данных, созданием сделки и контакта с входящими данными.
