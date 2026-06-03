<?php

namespace App\Support;

class SidebarMenu
{
    public static function sectionOpen(string $section): bool
    {
        $config = config("sidebar-menu.{$section}");
        if (! $config) {
            return false;
        }

        $request = request();

        foreach ($config['routes'] ?? [] as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        foreach ($config['paths'] ?? [] as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    public static function sectionClass(string $section): string
    {
        return self::sectionOpen($section) ? 'open has-active' : '';
    }

    public static function linkClass(array $routes = [], array $paths = []): string
    {
        $request = request();

        foreach ($routes as $pattern) {
            if ($request->routeIs($pattern)) {
                return 'active';
            }
        }

        foreach ($paths as $pattern) {
            if ($request->is($pattern)) {
                return 'active';
            }
        }

        return '';
    }
}
