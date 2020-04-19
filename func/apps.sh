#!/usr/bin/env bash

# Verifica se o public_html é um diretório vazio
#
# Também é considerado vazio os diretórios que possuem apenas os arquivos padrão gerados pelo vesta
#
# Retorna "1" se o public_html estiver vazio
empty() {
    local web_path="$1"

    # Verifica se existe algum sistema instalado no diretório público caso ele exista e não esteja vazio
    if [[ -d "$web_path/public_html" && "$(ls -A "$web_path/public_html")" ]]; then
        # Lista de arquivos que não são considerados para identificar um sistema
        aceitos=( 'index.html' 'robots.txt' )
        for file_path in "$web_path/public_html/"*; do
            file=$(basename -- "$file_path")

            invalido="1"
            for item in "${aceitos[@]}"; do
                if [[ "$file" == "$item" ]]; then
                    invalido=""
                fi
            done

            # Não é um arquivo permitido para a instalação
            if [[ "$invalido" ]]; then
                return
            fi
        done
    fi

    echo "1"
}

check_web_dir() {
    local user_name="$1"
    local web_domain="$2"

    if [[ ! "$user_name" || ! "$web_domain" ]]; then
        echo "Invalid arguments"
        return
    fi

    web_path="/home/$user_name/web/$web_domain";

    # Check if web domain exist
    if [[ ! -d "$web_path" ]]; then
        echo "The web directory not exist."
        return
    fi

    # Check if public_html is empty
    if [[ "$(empty "$web_path")" != "1" ]]; then
        echo "The public_html is not empty."
        echo "You need delete the content manually or rename the directory to avoid data lost."
        return
    fi

    if [[ ! -d "$web_path/public_html" ]]; then
        mkdir -p "$web_path/public_html"
        chown "$user_name:$user_name" "$web_path/public_html"
    fi

    echo "1"
}

wordpress() {
    local user_name="$1"
    local web_domain="$2"

    web_path="/home/$user_name/web/$web_domain";

    if [[ "$(check_web_dir "$user_name" "$web_domain")" != "1" ]]; then
        check_web_dir "$user_name" "$web_domain"
        return
    fi

    echo "== Downloading Wordpress..."
    curl -L -J  'https://br.wordpress.org/latest-pt_BR.zip' -o "/home/$user_name/tmp/wordpress.zip" 2>&1

    echo -e "\n== Extract files..."
    unzip "/home/$user_name/tmp/wordpress.zip" -d "/home/$user_name/tmp"
    rm -f "/home/$user_name/tmp/wordpress.zip"

    # Change owner
    chown "$user_name:$user_name" -R "/home/$user_name/tmp/wordpress"
    # Clean up vesta initial files
    rm -rf "$web_path/public_html/index.html" "$web_path/public_html/robots.txt"
    # Move files to the public_html
    mv "/home/$user_name/tmp/wordpress/"* "$web_path/public_html"
    rm -rf "/home/$user_name/tmp/wordpress"

    echo -e "\nInstallation completed"
}

moodle() {
    local user_name="$1"
    local web_domain="$2"

    web_path="/home/$user_name/web/$web_domain";

    if [[ "$(check_web_dir "$user_name" "$web_domain")" != "1" ]]; then
        check_web_dir "$user_name" "$web_domain"
        return
    fi

    echo "== Downloading Moodle..."
    curl -L -J  'https://download.moodle.org/stable38/moodle-latest-38.zip' -o "/home/$user_name/tmp/moodle.zip" 2>&1

    echo -e "\n== Extract files..."
    unzip "/home/$user_name/tmp/moodle.zip" -d "/home/$user_name/tmp"
    rm -f "/home/$user_name/tmp/moodle.zip"

    # Change owner
    chown "$user_name:$user_name" -R "/home/$user_name/tmp/moodle"
    # Clean up vesta initial files
    rm -rf "$web_path/public_html/index.html" "$web_path/public_html/robots.txt"
    # Move files to the public_html
    mv "/home/$user_name/tmp/moodle/"* "$web_path/public_html"
    mv "/home/$user_name/tmp/moodle/".* "$web_path/public_html"
    rm -rf "/home/$user_name/tmp/moodle"

    echo -e "\nInstallation completed"
}





