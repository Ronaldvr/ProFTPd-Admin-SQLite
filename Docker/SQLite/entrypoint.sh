#!/bin/bash


# Start the primary process and put it in the background
#proftpd --nodaemon &
proftpd

# Start the helper process
/usr/local/bin/docker-php-entrypoint

