<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc2141a075b5f4c1bfe17704d2f310787
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phroute\\Phroute\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phroute\\Phroute\\' => 
        array (
            0 => __DIR__ . '/..' . '/phroute/phroute/src/Phroute',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pug\\' => 
            array (
                0 => __DIR__ . '/..' . '/pug-php/pug/src',
            ),
        ),
        'J' => 
        array (
            'JsPhpize' => 
            array (
                0 => __DIR__ . '/..' . '/js-phpize/js-phpize/src',
            ),
            'Jade\\' => 
            array (
                0 => __DIR__ . '/..' . '/pug-php/pug/src',
            ),
        ),
        'G' => 
        array (
            'Gregwar\\Image' => 
            array (
                0 => __DIR__ . '/..' . '/gregwar/image',
            ),
            'Gregwar\\Cache' => 
            array (
                0 => __DIR__ . '/..' . '/gregwar/cache',
            ),
        ),
    );

    public static $classMap = array (
        'Eventviva\\ImageResize' => __DIR__ . '/..' . '/eventviva/php-image-resize/lib/ImageResize.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc2141a075b5f4c1bfe17704d2f310787::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc2141a075b5f4c1bfe17704d2f310787::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc2141a075b5f4c1bfe17704d2f310787::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc2141a075b5f4c1bfe17704d2f310787::$classMap;

        }, null, ClassLoader::class);
    }
}
