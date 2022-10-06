<?php

namespace App;

use App\Dispatcher\Dispatcher;
use App\Listener\Listener;
use Doctrine\Common\Annotations\AnnotationRegistry;

require_once __DIR__ . '/../vendor/autoload.php';
define('ROOT_DIR', __DIR__ . '\\');

class Application
{
    public static function launch(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
        $dispatcher = new Dispatcher(__DIR__ . "\Controller");
        $listener = new Listener($dispatcher);
        $listener->startListening();
    }
}

Application::launch();
