FROM php:apache

# install the ssl-cert package which will create a "snakeoil" keypair
RUN apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y ssl-cert \
 && rm -r /var/lib/apt/lists/*

# enable ssl module and enable the default-ssl site
RUN a2enmod ssl \
 && a2ensite default-ssl
 #

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo pdo_mysql 
# install other PHP modules
RUN apt-get update -y && apt-get install -y sendmail libpng-dev

RUN apt-get update && apt-get install -y zlib1g-dev 
RUN docker-php-ext-install gd

# not needed RUN docker-php-ext-install mbstring

# not needed RUN docker-php-ext-install zip

#
# install cron and load crontab
# =============================
RUN apt-get update && apt-get -y install cron

# Copy cron file to the cron.d directory
COPY crontab/cronjobs.txt /tmp/my-cron

# Give execution rights on the cron job
RUN chmod 0644 /tmp/my-cron

# Add cronjobs to crontab
RUN crontab /tmp/my-cron

# Install email
# =============
ARG SMTP_PASSWORD=AdkgJqW1Zo
# install
RUN apt-get install -q -y msmtp mailutils
# config
COPY msmtprc /etc/msmtprc
RUN chmod 600 /etc/msmtprc
RUN chown www-data:www-data /etc/msmtprc
ARG SMTP_PASSWORD=AdkgJqW1Zo
RUN sed -i "s|YourAwesomeStr0ngP4zzw0rd|$SMTP_PASSWORD|g" /etc/msmtprc
# Set up php sendmail config
RUN echo "sendmail_path=/usr/bin/msmtp -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini


# Set Timezone to Europe/Amsterdam
# ================================
RUN apt-get update && \
    apt-get -y install tzdata

RUN cp /usr/share/zoneinfo/Europe/Amsterdam /etc/localtime && \
    echo "Europe/Amsterdam" > /etc/timezone

#RUN apt-get -y remove tzdata
RUN rm -rf /var/cache/apk/*

# set timezone in php-apache
# ==========================
ADD devsetup.ini /usr/local/etc/php/conf.d/
ADD devsetup.conf /etc/apache2/conf-available/
RUN a2enconf devsetup
RUN a2enmod rewrite
# skipped apache2 restart because apache2 is restarted below for cron service
# RUN service apache2 restart
#
# restart Apache with cron service
# ================================
RUN sed -i 's/^exec /service cron start\n\nexec /' /usr/local/bin/apache2-foreground


