Mediastorage.app
=============================

Тестовое задание на Symfony3

УСТАНОВКА
------------

### Клонирование
studygit.simbirsoft1.com/supremo_raf/mediastorage.git


### Установка зависимостей
Установка
    php5   
	mysql-server
	nginx (был использован в данном случае)
	

### Настройка
1) parameters.yml
параметры подключения к бд

2) создать бд
php bin/console doctrine:database:create

3) обновить бд
php bin/console doctrine:schema:update --force

4) выполнить заполнение бд случайными записями Faker
php bin/console faker:populate



