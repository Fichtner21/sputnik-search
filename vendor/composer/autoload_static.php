<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb45d3958c9deea5128e0d88875a0eb29
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb45d3958c9deea5128e0d88875a0eb29::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb45d3958c9deea5128e0d88875a0eb29::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
