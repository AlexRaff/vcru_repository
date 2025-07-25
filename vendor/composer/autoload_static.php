<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit864f2821bf0eed722b5a5846e04f3a9a
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit864f2821bf0eed722b5a5846e04f3a9a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit864f2821bf0eed722b5a5846e04f3a9a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit864f2821bf0eed722b5a5846e04f3a9a::$classMap;

        }, null, ClassLoader::class);
    }
}
