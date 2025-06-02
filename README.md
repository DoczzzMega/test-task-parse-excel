
# Laravel Project (тестовое задание)

Этот проект создан с использованием фреймворка Laravel. Ниже приведены шаги для запуска проекта.

## О проекте

Проект представляет собой парсер  excel файлов (xlsx) для импорта данных таблицы в базу данных. Использованы laravel очереди (horizon для запуска очередей), вебсокеты с сервером reverb, для динамического отображения обработанных строк файла и уведомления об успехе операции. По итогу генерируется файл result.txt с отчетом об ошибках валидации строк excel файла. 
<p> Ссылка на тестовое задание:</p>

- [Backend-developer тестовое задание](https://docs.google.com/document/d/1rz-qUjbMjuIinNHUzKe8yfHKSs6UjqP4_Vn2uvzhmlk/edit?tab=t.0).

## Установка

1. Клонируйте репозиторий:

    ```bash
    git clone git@github.com:DoczzzMega/test-task-parse-excel.git
    ```
   
2. Перейдите в директорию проекта:

    ```bash
    cd your-laravel-project
    ```
   
3. Установите зависимости:

    ```bash
    composer install
    ```
    ```bash
    npm install
    ```

4. Скопируйте файл `.env.example` в `.env` и настройте файл `.env` с вашими конфигурациями:

    ```bash
    cp .env.example .env
    ```

5. Сгенерируйте ключ приложения:

    ```bash
    php artisan key:generate
    ```
6. Выполните миграции для создания таблиц в базе данных:

    ```bash
    php artisan migrate
    ```

7. Выполните команду создания пользователя для доступа к роутам. Логин: `admin@mail.com` Пароль: `password`

    ```bash
    php artisan user:create
    ```

### Запуск воркеров и reverb сервера

1. Запуск horizon:

    ```bash
    php artisan horizon
    ```
   
2. Запуск reverb:

    ```bash
    php artisan reverb:start
    ```
