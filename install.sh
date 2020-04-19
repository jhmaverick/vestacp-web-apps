#!/usr/bin/env bash

# Check se o script esta rodando com root
if [[ "$(id -u)" != "0" ]]; then
   echo "This script must be run as root" >&2
   exit 1
fi

if [[ ! -d /usr/local/vesta ]]; then
    echo "Vesta CP is not installed." >&2
    exit 1
fi

chmod +x /usr/local/vesta/web/plugins/app-installer/bin/v-app-installer
ln -s /usr/local/vesta/web/plugins/app-installer/bin/v-app-installer /usr/local/vesta/bin

# Adiciona o link no menu do painel
if grep -q "\"/app-installer\"" /usr/local/vesta/web/templates/admin/panel.html; then
    echo 'Link já existe no menu.'
else
    sed -i "/<div class=\"l-menu clearfix noselect\">/a <div class=\"l-menu__item <?php if(\$TAB == \"$menu_name\" ) echo \"l-menu__item--active\" ?>\"><a href=\"$page_link\"><?=__(\"$menu_name\")?></a></div>" /usr/local/vesta/web/templates/admin/panel.html
fi



