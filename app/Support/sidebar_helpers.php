<?php

use App\Support\SidebarMenu;

if (! function_exists('sidebar_section_class')) {
    function sidebar_section_class(string $section): string
    {
        return SidebarMenu::sectionClass($section);
    }
}

if (! function_exists('sidebar_link_class')) {
    function sidebar_link_class(array $routes = [], array $paths = []): string
    {
        return SidebarMenu::linkClass($routes, $paths);
    }
}
