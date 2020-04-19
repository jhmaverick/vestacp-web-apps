<?php

$TAB="Plugins";

include($_SERVER['DOCUMENT_ROOT'] . "/inc/main.php");

// Header
if (!isset($_GET['action'])) {
    include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.html');
}

// Panel
if (!isset($_GET['action'])) {
    top_panel($user, $TAB);
}

echo "<div class=\"l-center units app-installer\">" .
    "<link rel=\"stylesheet\" href=\"/app-installer/app-installer.css\"/>";

if (isset($_POST['app']) && !empty($_POST['app']) &&
    isset($_POST['web_domain']) && !empty($_POST['web_domain'])
) {
    $app = trim($_POST['app']);
    $web_parts = explode("|", $_POST['web_domain']);
    $user_name = trim($web_parts[0]);
    $web_domain = trim($web_parts[1]);

    $web_path = "/home/$user_name/web/$web_domain";

    echo "<pre>";
//    echo VESTA_CMD . "v-app-installer \"$app\" \"$user_name\" \"$web_domain\"";
    system(VESTA_CMD . "v-app-installer \"$app\" \"$user_name\" \"$web_domain\"");
    echo "</pre>";

    $backbutton = $_SERVER['REQUEST_URI'];
} else {
    $invalidos = [];
    ?>
    <form action="index.php" method="post">
        <h1>Instalador WordPress</h1>

        <select name="app" class="vst-list" required>
            <option value="">App</option>
            <option value="wordpress">Wordpress</option>
            <option value="moodle">Moodle</option>
        </select>
        <br><br>

        <select name="web_domain" class="vst-list" required>
            <option value="">Selecione um domínio</option>
            <?php
            exec(VESTA_CMD . "v-list-users json", $output, $return_var);

            $users = json_decode(implode('', $output), true);
            $users = array_reverse($users, true);
            foreach ($users as $user_name => $value) {
                exec(VESTA_CMD . "v-list-web-domains " . $user_name . " json", $web_output, $return_var);

                $data = json_decode(implode('', $web_output), true);
                $users[$user_name]['WEB_DOMAINS_LIST'] = array_reverse($data, true);
            }
            foreach ($users as $user_name => $user_data) {
                foreach ($user_data['WEB_DOMAINS_LIST'] as $web_domain => $domain_data) {
                    if ($_SESSION['user'] == 'admin' || $_SESSION['user'] == $user_name) {
                        echo "<option value=\"$user_name|$web_domain\">$web_domain($user_name)</option>";
                    } else {
                        $invalidos[] = ['user' => $user_name, 'web_domain' => $web_domain];
                    }
                }
            }
            ?>
        </select>
        <br><br>

        <button class="button confirm" type="submit">Instalar</button>
    </form>
    <?php
}

// Rodapé do Vesta
if (isset($backbutton) && $backbutton !== false) {
    echo "<div style=\"margin: 60px 0 30px;\">" .
        "<button class=\"button cancel\" onclick=\"location.href='" . $backbutton . "'\">" . __('Back') . "</button>" .
        "</div>";
}

echo "</div>";

include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/scripts.html');
include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.html');

