#!/bin/bash

/usr/sbin/init

# Run Laravel scheduler every minute
while [ true ]
do
  php artisan schedule:run --verbose --no-interaction
  sleep 60
done
