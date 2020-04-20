<?php

// Tab name
$TAB = "App Installer";

// Include vesta functions
include($_SERVER['DOCUMENT_ROOT'] . "/inc/main.php");
// Header
include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.html');
// Panel
top_panel($user, $TAB);

// Start content block
echo '<div class="l-center units app-installer">';
echo '<link rel="stylesheet" href="/plugins/app-installer/style.css"/>';

// The template used when there is no action
function default_template() {
    global $user;
    ?>
    <form action="index.php" method="post">
        <h1><?= __("App Installer") ?></h1>

        <select name="app" class="vst-list" required>
            <option value=""><?= __("Select an app") ?></option>
            <option value="wordpress">Wordpress</option>
            <option value="moodle">Moodle</option>
        </select>
        <br><br>

        <select name="web_domain" class="vst-list" required>
            <option value=""><?= __("Select a web domain") ?></option>
            <?php
            $users_output = [];
            exec(VESTA_CMD . "v-list-users json", $users_output);

            $users = json_decode(implode('', $users_output), true);
            $users = array_reverse($users, true);
            foreach ($users as $user_name => $value) {
                $web_output = [];
                exec(VESTA_CMD . "v-list-web-domains " . $user_name . " json", $web_output);

                $web_domains = json_decode(implode('', $web_output), true);
                $web_domains = array_reverse($web_domains, true);

                foreach ($web_domains as $web_domain => $domain_data) {
                    if ($user == 'admin' || $user == $user_name) {
                        $display_name = ($_SESSION['user'] == 'admin') ? "$user_name - $web_domain" : "$web_domain";

                        echo "<option value=\"$user_name|$web_domain\">$display_name</option>";
                    }
                }
            }
            ?>
        </select>
        <br><br>

        <input type="hidden" name="action" value="install"/>
        <button class="button confirm" type="submit"><?= __("Install") ?></button>
    </form>
<?php }

// Install app
function action_install($app, $user_name, $web_domain) {
    echo "<h1>" . __("Installing") . " $app...</h1>";

    echo "<pre>";
    system(VESTA_CMD . "v-add-web-domain-app \"$app\" \"$user_name\" \"$web_domain\"");
    echo "</pre>";

    global $backbutton;
    $backbutton = $_SERVER['REQUEST_URI'];
}

if (isset($_POST['action']) && $_POST['action'] == "install"
    && isset($_POST['app']) && !empty($_POST['app'])
    && isset($_POST['web_domain']) && !empty($_POST['web_domain'])
) {
    $app = trim($_POST['app']);
    $web_parts = explode("|", $_POST['web_domain']);
    $user_name = trim($web_parts[0]);
    $web_domain = trim($web_parts[1]);

    if ($user == 'admin' || $user == $user_name) {
        action_install($app, $user_name, $web_domain);
    } else {
        echo __("You are not allowed to perform this action");
    }
} else {
    default_template();
}

// Check if back button was defined
if (isset($backbutton) && $backbutton !== false) {
    echo "<div style=\"margin: 60px 0 30px;\">" .
        "<button class=\"button cancel\" onclick=\"location.href='" . $backbutton . "'\">" . __('Back') . "</button>" .
        "</div>";
}

// End content block
echo "</div>";

// Footer
include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/scripts.html');
include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.html');

