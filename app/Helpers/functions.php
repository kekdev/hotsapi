<?php

/**
 * Return nav-here if current path begins with this path.
 *
 * @return string
 */
function setActive(string $path)
{
    return Request::is($path) ? ' class=active' :  '';
}
