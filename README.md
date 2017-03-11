Mediastorage.app
=============================

Тестовое задание на Symfony3

Медиахранилище. Пользователю могут загружать файлы на сервер. Администраторы могут просматривать загруженные файлы, редактировать и удалять их. После загрузки файла пользователю приходит email с ссылкой на файл.

УСТАНОВКА
------------

### 1. Клонирование

studygit.simbirsoft1.com/supremo_raf/mediastorage.git
для удобства переименовать папку в mediastorage

### 2. Установка Homestead

	vagrant box add laravel/homestead
	
### 3. Конфигурация Symfony приложения

	1) composer install
	
	2) установки в parameters.yml
		bd: homestead
		login: homestead
		password: secret
	остальное - по умолчанию
		
### 4. Конфигурация Homestead

	1) Настройка Homestead.yaml: 
	Указать путь до директории mediastorage
	Примеры: 
	- map: d:\project
	- map: ~/Code
		
	2) Файл hosts:
	192.168.10.10  mediastorage.app
	  
	3) Запуск сервера
	vagrant up
		
### 5. Конфигурация RabbitMQ

	1) запуск отправителя
	cd Code/mediastorage
	php bin/console queue declare
	
	2) запуск слушателя
	php bin/console queue listen
	
	3) 	http://192.168.10.10:15672
		login: test
		password: test
	
	4) пробуем загрузить файл на сервер
	
### Дополнительно:

	1) доступ к таблице всех записей mediastorage/list
	Логин:	admin
	Пароль: adminpass

	2) faker добавить записей 
	php bin/console faker:populate
	
	количество добавляемых записей (config.yml)
	bazinga_faker:
		...
		number: ...
		
	3) возможная ошибка при отправке сообщений в Windows:
	Connection could not be established with host smtp.gmail.com [ #0]
	Препятствовал антивирус (в моем случае Avast)
	
