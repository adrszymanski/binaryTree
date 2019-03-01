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

INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (1, 'Root', 3, 4, 0, 0, 0, '');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (2, 'Left', 1, 1, 1, 1, 1, '1');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (19, 'Right', 1, 2, 1, 0, 1, '1');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (4, 'Left Left', 3, 3, 2, 1, 2, '1/2');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (18, 'Left Right', 1, 1, 2, 0, 2, '1/2');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (20, 'Right Right', 0, 1, 19, 0, 2, '1/19');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (21, 'Right Left', 0, 0, 19, 1, 2, '1/19');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (22, 'Right Right Right', 0, 0, 20, 0, 3, '1/19/20');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (24, 'Left Left Left', 1, 1, 4, 1, 3, '1/2/4');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (25, 'Left Left Right', 1, 1, 4, 0, 3, '1/2/4');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (26, 'Left Right Left', 0, 0, 18, 1, 3, '1/2/18');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (27, 'Left Right Right', 0, 0, 18, 0, 3, '1/2/18');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (29, 'Left Left Left Left', 0, 0, 24, 1, 4, '1/2/4/24');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (30, 'Left Left Left Right', 0, 0, 24, 0, 4, '1/2/4/24');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (31, 'Left Left  Right Right', 0, 0, 25, 0, 4, '1/2/4/25');
INSERT INTO `node` (`id`, `name`, `credits_left`, `credits_right`, `parent_id`, `is_left`, `depth`, `parents`) VALUES (32, 'Left Left Right Left', 0, 0, 25, 1, 4, '1/2/4/25');

5. Run PHP server with php -S 127.0.0.1:8000 -t public/
6. Open src/js/index.html in Browser
7. To enable localhost cors please open Chrome's directory and use cmd/powershell to
  open chrome without CORS: chrome.exe --user-data-dir="C://Chrome dev session" --disable-web-security
8. If you have any futher questions:
    507 663 889 or adr.szymanski@gmail.com


<-- Used dependencies from composer -->
annotations
symfony/orm-pack
symfony/maker-bundle