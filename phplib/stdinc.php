<?php
define("SITE_DIR",realpath(__DIR__."/../"));
require_once("Config.php");
require_once("ui.php");
require_once("utils.php");
require_once(SITE_DIR."/vendor/autoload.php");

$Config = new Config(SITE_DIR."/etc/site.ini");

session_name("oauthtester");
session_start();
echo "hi";