#!/usr/bin/env bash

# Check se o script esta rodando com root
if [[ "$(id -u)" != "0" ]]; then
   echo "This script must be run as root" >&2
   exit 1
fi

if [[ -d "/usr/local/vesta/web/plugins/" ]]; then
for f in /home/*; do

fi


# Remove from CLI
rm -f /usr/local/vesta/bin/v-app-installer
# Remove plugin files
rm -rf /usr/local/vesta/web/plugins/app-installer
