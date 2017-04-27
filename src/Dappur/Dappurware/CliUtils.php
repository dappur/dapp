<?php

namespace Dappur\Dappurware;
use Illuminate\Database\Capsule\Manager as Capsule;

class CliUtils {
	public static function isCamelCase($className) {
        return (bool) preg_match('/^([A-Z][a-z0-9]+)+$/', $className);
    }

    public function isDappur(){

    	$cwd = getcwd();
        $settings = realpath($cwd . '/app/bootstrap/settings.php');
        $settings_dist = realpath($cwd . '/app/bootstrap/settings.php.dist');

        if (file_exists($settings)) {
        	$settings = require $cwd . '/app/bootstrap/settings.php';

        	if ($settings['settings']['framework'] == 'dappur') {
        		if ($settings['settings']['db']['host'] != "" && $settings['settings']['db']['database'] != "" && $settings['settings']['db']['username'] != "" && $settings['settings']['db']['password'] != "") {

        			$capsule = new Capsule;
					$capsule->addConnection([
					    'driver'    => 'mysql',
					    'host'      => $settings['settings']['db']['host'],
					    'database'  => $settings['settings']['db']['database'],
					    'username'  => $settings['settings']['db']['username'],
					    'password'  => $settings['settings']['db']['password'],
					    'charset'   => 'utf8',
					    'collation' => 'utf8_unicode_ci',
					    'prefix'    => '',
					]);
					$capsule->setAsGlobal();
					$capsule->bootEloquent();

					try {
						Capsule::connection()->getPdo();
						return true;
					} catch(\Exception $e){
						throw new \InvalidArgumentException('There was an error connecting to your project database.');
					}

        		}else{
        			throw new \InvalidArgumentException('Dappur project detected but does not appear to be set up.');
        		}
        	}else{
        		throw new \InvalidArgumentException('Dappur project not detected.');
        	}
        }else if (file_exists($settings_dist) && !file_exists($settings)){
        	throw new \InvalidArgumentException('Dappur project detected but does not appear to be set up.');
        }else{
        	throw new \InvalidArgumentException('Dappur project not detected.');
        }

    }
    
}