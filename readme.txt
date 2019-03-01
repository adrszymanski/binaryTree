<-- Technologies used -->
PHP with Symfony Framework / Symfony Skeleton
Doctrine ORM
MySQL Database
Pure javascript with axios

Steps:
1. Create database and add it to .env or simply add binary_tree db to localhost mysql(root, root).
2. Create migration: php bin/console make:migration
3. Migrate to DB: php bin/console doctrine:migrations:migrate
4. SQL INSERT with some nodes:

INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (1, 'Root', 3, 4, NULL, 0);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (2, 'Left', 1, 1, 1, 1);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (4, 'Left Left', 0, 0, 2, 1);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (19, 'Right', 1, 2, 1, 0);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (20, 'Right Right', 0, 1, 19, 0);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (21, 'Right Left', 0, 0, 19, 1);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (22, 'Right Right Right', 0, 0, 20, 0);
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`) VALUES (18, 'Left Right', 0, 0, 2, 0);

4. Run PHP server with php -S 127.0.0.1:8000 -t public/
5. Open src/js/index.html in Browser
6. To enable localhost cors please open Chrome's directory and use cmd/powershell to
  open chrome without CORS: chrome.exe --user-data-dir="C://Chrome dev session" --disable-web-security
7. If you have any futher questions:
    507 663 889 or adr.szymanski@gmail.com


<-- Used dependencies from composer -->
annotations
symfony/orm-pack
symfony/maker-bundle
serializer