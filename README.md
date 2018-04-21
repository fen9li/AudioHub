## About audiohub

## Versions
**Release v1.0.7**

## Description
Configure queue work running on cron.

## Usage
### Setup laradb
* Build laradb.fen9.li to provide MariaDB database service.
```
hostname=laradb.fen9.li
hostnamectl set-hostname $hostname

ipaddr="192.168.200.78/24"
nmcli con add con-name ens33-fix type ethernet ifname ens33 autoconnect yes ip4 $ipaddr gw4 192.168.200.2
nmcli con mod ens33-fix ipv4.dns 192.168.200.2

ipaddr="192.168.224.78/24"
nmcli con add con-name ens35-fix type ethernet ifname ens35 autoconnect yes ip4 $ipaddr
echo "192.168.224.0/24 via $ipaddr dev ens35" > /etc/sysconfig/network-scripts/route-ens35

echo '192.168.200.77  laradev.fen9.li    laradev' >> /etc/hosts
echo '192.168.200.78  laradb.fen9.li     laradb' >> /etc/hosts
echo '192.168.200.79  laraweb.fen9.li    laraweb' >> /etc/hosts

usermod -aG wheel fli
shutdown -r now

firewall-cmd --permanent --add-service=mysql
firewall-cmd --reload
```

* Install MariaDB
```
[root@laradev ~]# vim /etc/yum.repos.d/MariaDB.repo
[root@laradev ~]# cat /etc/yum.repos.d/MariaDB.repo
# MariaDB 10.2 CentOS repository list - created 2018-03-08 09:09 UTC
# http://downloads.mariadb.org/mariadb/repositories/
[mariadb]
name=MariaDB
baseurl=http://yum.mariadb.org/10.2/centos7-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1
[root@laradev ~]#  

yum -y install MariaDB-server MariaDB-client
systemctl start mariadb
systemctl enable mariadb
systemctl status mariadb

mysql_secure_installation
```

* Create databases
```
[fli@laradev ~]$ mysql -u root -pxxxxxxxx
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 16
Server version: 10.2.13-MariaDB MariaDB Server

Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]> SHOW DATABASES;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| mysql              |
| performance_schema |
+--------------------+
3 rows in set (0.00 sec)

MariaDB [(none)]> 

CREATE DATABASE audiohub;
CREATE USER 'fli'@'localhost' IDENTIFIED BY 'xxxxxxxxx';
CREATE USER 'fli'@'127.0.0.1' IDENTIFIED BY 'xxxxxxxxx';
CREATE USER 'fli'@'%' IDENTIFIED BY 'xxxxxxxxx';
GRANT ALL ON audiohub.* TO 'fli'@'localhost';
GRANT ALL ON audiohub.* TO 'fli'@'127.0.0.1';
GRANT ALL ON audiohub.* TO 'fli'@'%';

MariaDB [(none)]> SHOW GRANTS FOR 'fli';
+----------------------------------------------------------------------------------------------------+
| Grants for fli@%                                                                                   |
+----------------------------------------------------------------------------------------------------+
| GRANT USAGE ON *.* TO 'fli'@'%' IDENTIFIED BY PASSWORD '*B1DB2DE8AC6C31CE2A11815BE0C0AD4F195BE53F' |
| GRANT ALL PRIVILEGES ON `audiohub`.* TO 'fli'@'%'                                                  |
+----------------------------------------------------------------------------------------------------+
2 rows in set (0.00 sec)

MariaDB [(none)]> QUIT
Bye
[fli@laradev ~]$
```

### Setup laraweb
* Build laraweb.fen9.li to provide web service.
```
hostname=laraweb.fen9.li
hostnamectl set-hostname $hostname

ipaddr="192.168.200.79/24"
nmcli con add con-name ens33-fix type ethernet ifname ens33 autoconnect yes ip4 $ipaddr gw4 192.168.200.2
nmcli con mod ens33-fix ipv4.dns 192.168.200.2

ipaddr="192.168.224.79/24"
nmcli con add con-name ens35-fix type ethernet ifname ens35 autoconnect yes ip4 $ipaddr
echo "192.168.224.0/24 via $ipaddr dev ens35" > /etc/sysconfig/network-scripts/route-ens35

echo '192.168.200.77  laradev.fen9.li    laradev' >> /etc/hosts
echo '192.168.200.78  laradb.fen9.li     laradb' >> /etc/hosts
echo '192.168.200.79  laraweb.fen9.li    laraweb' >> /etc/hosts

usermod -aG wheel fli
shutdown -r now

firewall-cmd --permanent --add-service={http,https}
firewall-cmd --reload
```

* Install software packages
```
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

yum -y install php70w php70w-opcache php70w-mbstring php70w-xml php70w-pdo php70w-devel php70w-pear php70w-mysql

yum -y install httpd
systemctl start httpd
systemctl enable httpd
systemctl status httpd

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### Setup application
* Clone audiohub repo
```
git clone https://github.com/fen9li/AudioHub.git /var/www/audiohub
```

* Update composer.json
```
[fli@laraweb audiohub]$ vim composer.json
[fli@laraweb audiohub]$ sed -n '35,41p' composer.json
    "extra": {
        "laravel": {
            "dont-discover": [
               "laravel/dusk"
            ]
        }
    },
[fli@laraweb audiohub]$
```

* Setup application 
```
cd /var/www/audiohub
cp .env.example .env
composer update
php artisan key:generate
```

* Update .env
```
[fli@laraweb audiohub]$ vim .env
[fli@laraweb audiohub]$ cat .env
APP_URL=http://laraweb.fen9.li
APP_NAME=AudioHub
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=laradb.fen9.li
DB_PORT=3306
DB_DATABASE=xxxxxxxx
DB_USERNAME=xxx
DB_PASSWORD=xxxxxxxxx

MAIL_DRIVER=smtp
MAIL_HOST=xxxx.xxxxx.xxx
MAIL_PORT=xxx
MAIL_USERNAME=xxxxxxxxxx@xxxxx.xxx
MAIL_FROM_ADDRESS=xxxxxxxxxx@xxxxx.xxx
MAIL_FROM_NAME=AudioHub
MAIL_PASSWORD=xxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
QUEUE_DRIVER=database

APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_LOG_LEVEL=xxxx

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
[fli@laraweb audiohub]$
```

* Migrate database
```
[fli@laraweb audiohub]$ php artisan migrate
**************************************
*     Application In Production!     *
**************************************

 Do you really wish to run this command? (yes/no) [no]:
 > yes

Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
Migrating: 2018_04_10_234239_add_verified_to_users_table
Migrated:  2018_04_10_234239_add_verified_to_users_table
Migrating: 2018_04_10_234409_create_email_verifications_table
Migrated:  2018_04_10_234409_create_email_verifications_table
Migrating: 2018_04_19_232105_create_jobs_table
Migrated:  2018_04_19_232105_create_jobs_table
Migrating: 2018_04_19_232141_create_failed_jobs_table
Migrated:  2018_04_19_232141_create_failed_jobs_table
[fli@laraweb audiohub]$
```

* Update '/etc/httpd/conf/httpd.conf' and restart httpd
```
[root@laraweb audiohub]# vim /etc/httpd/conf/httpd.conf
[root@laraweb audiohub]# sed -n '115,130p' /etc/httpd/conf/httpd.conf
# DocumentRoot: The directory out of which you will serve your
# documents. By default, all requests are taken from this directory, but
# symbolic links and aliases may be used to point to other locations.
#
#DocumentRoot "/var/www/html"

DocumentRoot "/var/www/audiohub/public"

#
# Relax access to content within /var/www/audiohub/public.
#
<Directory "/var/www/audiohub/public">
    Options Indexes FollowSymLinks
    AllowOverride all
    Require all granted
</Directory>
[root@laraweb audiohub]#

[root@laraweb audiohub]# systemctl restart httpd
[root@laraweb audiohub]#
```

* Setup cron to run queue work every minute
```
[fli@laradev ~]$ crontab -e
no crontab for fli - using an empty one
crontab: installing new crontab
[fli@laradev ~]$ crontab -l
* * * * * php /var/www/audiohub/artisan queue:work >> /dev/null 2>&1
[fli@laradev ~]$
```

### Run application
* http://laraweb.fen9.li

## License
This audiohub project is a open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
