FROM centos:7
LABEL maintainer="lifcn@yahoo.com"

# Enable systemd
ENV container docker
RUN (cd /lib/systemd/system/sysinit.target.wants/; for i in *; do [ $i == \
systemd-tmpfiles-setup.service ] || rm -f $i; done); \
rm -f /lib/systemd/system/multi-user.target.wants/*;\
rm -f /etc/systemd/system/*.wants/*;\
rm -f /lib/systemd/system/local-fs.target.wants/*; \
rm -f /lib/systemd/system/sockets.target.wants/*udev*; \
rm -f /lib/systemd/system/sockets.target.wants/*initctl*; \
rm -f /lib/systemd/system/basic.target.wants/*;\
rm -f /lib/systemd/system/anaconda.target.wants/*;
VOLUME [ "/sys/fs/cgroup" ]

# install httpd and enable httpd service
RUN yum -y install httpd; yum clean all; systemctl enable httpd.service

# install php 7.0
RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
RUN rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
RUN yum -y install php70w php70w-opcache php70w-mbstring php70w-xml php70w-pdo php70w-devel php70w-pear php70w-mysql

# Setup laravel app
#COPY phpinfo.php /var/www/html/
COPY httpd.conf /etc/httpd/conf/httpd.conf
COPY . /var/www/audiohub
WORKDIR /var/www/audiohub
RUN chown -R apache: /var/www/audiohub
RUN chmod 0755 /var/www/audiohub
RUN rm --force /var/www/audiohub/bootstrap/cache/*

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
CMD ["/usr/local/bin/entrypoint.sh"]
