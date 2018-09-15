<?php

namespace Dappur\Dappurware;

use Illuminate\Database\Capsule\Manager as Capsule;

class CliUtils
{
    public static function isCamelCase($className)
    {
        return (bool) preg_match('/^([A-Z][a-z0-9]+)+$/', $className);
    }

    public function isDappur()
    {
        $cwd = getcwd();
        $settings = realpath($cwd . '/settings.json');
        $settingsDist = realpath($cwd . '/settings.json.dist');

        if (file_exists($settingsDist) && !file_exists($settings)) {
            throw new \InvalidArgumentException(
                'Dappur project detected but does not appear to be set up.'
            );
        }

        if (!file_exists($settings)) {
            throw new \InvalidArgumentException('Dappur project not detected.');
        }

        $settings = json_decode(file_get_contents($settings), true);
        $environment = $settings['environment'];
        if ($settings['db'][$environment]['host'] != ""
            && $settings['db'][$environment]['database'] != ""
            && $settings['db'][$environment]['username'] != ""
        ) {
            return CliUtils::checkDB($settings['db'][$environment]);
        }
        
        throw new \InvalidArgumentException(
            'Dappur project detected but does not appear to be set up.'
        );
    }

    public function checkDB($database)
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection($database);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        try {
            Capsule::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e);
        }
    }
}
