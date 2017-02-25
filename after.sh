#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.

#RABBITMQ
echo "RABBITMQ INSTALLATION..."
echo "deb http://www.rabbitmq.com/debian/ testing main"  | sudo tee  /etc/apt/sources.list.d/rabbitmq.list > /dev/null
wget -O- https://www.rabbitmq.com/rabbitmq-release-signing-key.asc | sudo apt-key add -
sudo apt-key add rabbitmq-signing-key-public.asc
sudo apt-get update
sudo apt-get install rabbitmq-server -y
sudo rabbitmq-plugins enable rabbitmq_management
sudo rabbitmqctl add_user test test
sudo rabbitmqctl set_user_tags test administrator
sudo rabbitmqctl set_permissions -p / test ".*" ".*" ".*"
echo "...IS COMPLETE"

#GETTING TO THE CORRECT DIRECTORY
cd /home/vagrant/Code/mediastorage

#CREATE TABLE
php bin/console doctrine:schema:update --force

#FILLING RANDOM ENTRIES 
echo "GET FAKE"
php bin/console faker:populate

