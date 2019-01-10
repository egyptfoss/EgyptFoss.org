#!/bin/bash

## Update, upgrade system packages, install required packages

echo "mysql-server-5.6 mysql-server/root_password password root" | sudo debconf-set-selections
echo "mysql-server-5.6 mysql-server/root_password_again password root" | sudo debconf-set-selections

apt-get update && apt-get dist-upgrade -y
apt-get install -y curl elinks lsof man vim git wget ca-certificates git-core curl zlib1g-dev build-essential libssl-dev libreadline-dev libyaml-dev libsqlite3-dev sqlite3 libxml2-dev libxslt1-dev libcurl4-openssl-dev python-software-properties virtualbox-guest-dkms nginx-extras php5-fpm php5-mysql php5-gd libssh2-php mysql-server-5.6

## Change TimeZone to Cairo

ln -sf /usr/share/zoneinfo/Africa/Cairo /etc/localtime
echo -n 'Africa/Cairo' > /etc/timezone

## Fix locale

export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8
locale-gen en_US.UTF-8
dpkg-reconfigure locales
update-locale
