<?php

use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Setting\SettingName as Name;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\PasswordHasher;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Filesystem\IOException;

require "vendor/autoload.php";

$get = new Hash($_GET);
try {
    try {
        Setting::readEncryptKey();
    } catch (IOException $exception) {
        Setting::createFileOfEncryptKey();
    }
    try {
        Setting::readEncryptIV();
    } catch (IOException $exception) {
        Setting::createFileOfEncryptIV();
    }
    $database = new Database();
    $database->connect();
    $setting = new Setting($database);
    $setting->edit();
    $password = new StringObject($setting->getRecord()->get(Name::const(Name::SETTING_PASSWORD)));
    $newPassword = new StringObject($get->get(Name::const(Name::SETTING_PASSWORD)));
    if (($password->length() == 0) != ($newPassword->length() == 0)) {
        if ($newPassword->length() > 0) {
            $passwordHasher = new PasswordHasher($newPassword);
            $setting->getRecord()->put(Name::const(Name::SETTING_PASSWORD), $passwordHasher->getHash());
        }
        $setting->update();
        echo "ok";
    } else {
        echo "ng";
    }
} catch (Exception $exception) {
    echo $exception->getMessage();
}
