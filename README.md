Mediastorage.app
=============================

Тестовое задание на Symfony3

УСТАНОВКА
------------

### Клонирование

studygit.simbirsoft1.com/supremo_raf/mediastorage.git

### Установка Homestead

	vagrant box add laravel/homestead

### Конфигурация Homestead

	1) Настройка Homestead.yaml: 
	Указать путь до директории mediastorage
	Примеры: 
	- map: d:\project
	- map: ~/Code
		
	2) Файл hosts:
	192.168.10.10  mediastorage.app
	  
	3) Запуск сервера
	vagrant up
	
### Конфигурация Symfony приложения

	1) composer install
	
	2) parameters.yml
	параметры подключения к бд:
		bd: homestead
		login: homestead
		password: secret
		
### Конфигурация RabbitMQ

	1) запуск отправителя
	php bin/console queue declare
	
	2) запуск слушателя
	php bin/console queue listen
	
	3) 	http://192.168.10.10:15672
		login: test
		password: test
	
### Дополнительно:

	1) Доступ к таблице всех записей mediastorage/list
	Логин:	admin
	Пароль: adminpass

	2) faker добавить записей 
	php bin/console faker:populate
	
	количество добавляемых записей (config.yml)
	bazinga_faker:
		...
		number: ...
