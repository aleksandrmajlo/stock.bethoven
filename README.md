# Stock Beethoven

Внутрішня CRM для складського обліку комплектів і запчастин: прийомка за barcode, друк етикеток, ремонтні замовлення, списання та контроль оплат.

## Стек

- PHP 8.3+, Laravel 13
- MySQL 8, Redis
- Alpine.js, Tailwind CSS, Vite
- Composer і Node.js/npm

## Локальний запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

Перед комітом:

```bash
php artisan test
npm run build
git diff --check
```

## Production через FileZilla

1. Зробити резервну копію бази даних і змінюваних файлів.
2. Локально виконати `npm ci` та `npm run build`.
3. Через SFTP завантажити змінені файли проєкту зі збереженням структури каталогів.
4. Окремо повністю завантажити `public/build` — ця папка навмисно не зберігається в Git.
5. Не перезаписувати production-файли `.env`, `storage`, `vendor` і `public/storage`.
6. На сервері, якщо доступний terminal, виконати:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan queue:restart
```

Document root домену повинен вказувати на каталог `public` цього проєкту.

## Безпека публічного репозиторію

- Не комітити `.env`, паролі, ключі, IP-адреси, дампи бази та файли доступів.
- Секрети зберігати тільки в environment-змінних production-сервера.
- Не змінювати production `APP_KEY` під час оновлення.
- Після випадкової публікації секрету його потрібно замінити: видалення з нового коміту не прибирає його з Git-історії.
- Не використовувати права `777`; запис дозволяти лише для `storage` і `bootstrap/cache`.

## Важливо

`public/build` ігнорується Git, тому після змін Tailwind, JavaScript або Blade-класів production-збірку потрібно передавати через FileZilla вручну.
