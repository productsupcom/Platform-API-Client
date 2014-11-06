<?php
/**
 * require this autoloader only if you do not use the composer installation
 */
namespace Productsup;
function autoload($className)
{
    if(strpos($className,'Productsup') === false) return false;
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require __DIR__.'/lib/'.$fileName;
}
\spl_autoload_register('Productsup\\autoload');