<?php

// Tab name
$TAB = "Web Apps";

// Include vesta functions
include($_SERVER['DOCUMENT_ROOT'] . "/inc/main.php");

if (isset($_POST['action']) && $_POST['action'] == "install"
    && isset($_POST['app']) && !empty($_POST['app'])
    && isset($_POST['web_domain']) && !empty($_POST['web_domain'])
) {
    $app = trim($_POST['app']);
    $web_parts = explode("|", $_POST['web_domain']);
    $user_name = trim($web_parts[0]);
    $web_domain = trim($web_parts[1]);

    if ($user == 'admin' || $user == $user_name) {
        $output = Vesta::exec('v-install-web-app', $app, $user_name, $web_domain);
    } else {
        $output = __("You are not allowed to perform this action");
    }

    Vesta::render_cmd_output($output, __("Installing") . " $app", $_SERVER['REQUEST_URI']);
} else {
    Vesta::render("/templates/install.php", ['plugin' => 'web-apps', 'data' => $data]);
}
