#!/bin/bash

if [ ! -f /app/web/sites/default/settings.local.php ]; then
    cp /app/web/sites/default/settings.local.php.sample /app/web/sites/default/settings.local.php
fi

if [ ! -d /app/web/sites/default/files ]; then
    mkdir -p /app/web/sites/default/files
    chmod -R 777 /app/web/sites/default/files
fi

if [ ! -d web/sites/default/private ]; then
    mkdir -p web/sites/default/private
    chmod -R 777 web/sites/default/private
fi

chmod 755 /app/web/sites/default