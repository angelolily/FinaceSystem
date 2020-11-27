<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7405bc605c1506daad95895b9c2dd9ac
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7405bc605c1506daad95895b9c2dd9ac::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7405bc605c1506daad95895b9c2dd9ac::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}