<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit57823f3d3771824c0285c8ea2003e4d5
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Jes490\\DParser\\' => 15,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Jes490\\DParser\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/jes490/dparser/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit57823f3d3771824c0285c8ea2003e4d5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit57823f3d3771824c0285c8ea2003e4d5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
