## Versions
**Release R1.0.0 **

## Description
1st release of AudioHub

## Usage
### Setup laradb
* Build laradb.fen9.li as MariaDB host to provide database service,queue service.

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
echo '192.168.200.79  laraprod.fen9.li   laraprod' >> /etc/hosts

usermod -aG wheel fli

firewall-cmd --permanent --add-service=mysql
firewall-cmd --reload

shutdown -r now

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
CREATE USER 'fli'@'%' IDENTIFIED BY 'xxxxxxxxx';
GRANT ALL ON audiohub.* TO 'fli'@'localhost';
GRANT ALL ON audiohub.* TO 'fli'@'%';

MariaDB [(none)]> SHOW GRANTS FOR 'fli';
+----------------------------------------------------------------------------------------------------+
| Grants for fli@%                                                                                   |
+----------------------------------------------------------------------------------------------------+
| GRANT USAGE ON *.* TO 'fli'@'%' IDENTIFIED BY PASSWORD '*B1DB2DE8AC6C31CE2A11815BE0C0AD4F195BE53F' |
| GRANT ALL PRIVILEGES ON `audiohub`.* TO 'fli'@'%'                                                  |
+----------------------------------------------------------------------------------------------------+
2 rows in set (0.00 sec)

MariaDB [(none)]> SELECT Host,User,Show_db_priv FROM mysql.user;
+-----------+------+--------------+
| Host      | User | Show_db_priv |
+-----------+------+--------------+
| localhost | root | Y            |
| 127.0.0.1 | root | Y            |
| ::1       | root | Y            |
| %         | fli  | N            |
| localhost | fli  | N            |
+-----------+------+--------------+
5 rows in set (0.00 sec)

MariaDB [(none)]> QUIT
Bye
[fli@laradev ~]$

```


### Setup laraprod
* Build laraprod.fen9.li as web host to provide web service.

```
hostname=laraprod.fen9.li
hostnamectl set-hostname $hostname

ipaddr="192.168.200.79/24"
nmcli con add con-name ens33-fix type ethernet ifname ens33 autoconnect yes ip4 $ipaddr gw4 192.168.200.2
nmcli con mod ens33-fix ipv4.dns 192.168.200.2

ipaddr="192.168.224.79/24"
nmcli con add con-name ens35-fix type ethernet ifname ens35 autoconnect yes ip4 $ipaddr
echo "192.168.224.0/24 via $ipaddr dev ens35" > /etc/sysconfig/network-scripts/route-ens35

echo '192.168.200.77  laradev.fen9.li    laradev' >> /etc/hosts
echo '192.168.200.78  laradb.fen9.li     laradb' >> /etc/hosts
echo '192.168.200.79  laraprod.fen9.li   laraprod' >> /etc/hosts

usermod -aG wheel fli

firewall-cmd --permanent --add-service={http,https}
firewall-cmd --reload

shutdown -r now

yum -y install policycoreutils-python
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

### Setup AudioHub

```
[fli@laraprod ~]$ git clone git@github.com:fen9li/AudioHub.git /var/www/audiohub
... ...
[fli@laraprod ~]$ cd /var/www/audiohub
[fli@laraprod audiohub]$

cp .env.example .env
composer update
php artisan key:generate

[fli@laraprod audiohub]$
...

```



