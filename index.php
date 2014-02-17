<?php



require "libraries/CyanStar/autoload.php";
require "libraries/differencer/functions.php";
require "libraries/differencer/object.php";
require "libraries/differencer/modelobject.php";
require "libraries/differencer/debugobject.php";
require "libraries/differencer/controller.php";
require "libraries/differencer/frontcontroller.php";



//similarityArrays("i   \n\r\t  am    a  dog treat", "i am a kid who likes to trick or treat"); exit;
//similarityArrays("i am a kid who likes to trick or treat", "i   \n\r\t  am    a  dog treat"); exit;

$front = new FrontController;
$front->execute();


