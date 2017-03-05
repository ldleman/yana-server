<?php

/**
 * Manage application and plugins configurations with key/value pair.
 * **nb:** It's possible to specify namespace in order to distinct global configuration to plugin custom configuration
 * @author valentin carruesco
 * @category Core
 * @license copyright
 */
class Configuration extends Entity
{
    public $id,$key,$value;
	protected $confTab;
    protected $fields =
    array(
        'id' => 'key',
        'key' => 'longstring',
        'value' => 'longstring',
    );

    /**
     * Get all configurations from database OR session if it was yet loaded
     * This function is called at start of program and global var '$conf' is filled with response, so use global $conf instead of call this function.
     * #### Example
     * ```php
     * $confs = Configuration::getAll();
     * var_dump($confs);
     * ```.
     * @return array Array of configurations
     */
    public function getAll()
    {
        if (!isset($_SESSION['configuration'])) {
            $configurationManager = new self();
            $configs = $configurationManager->populate();
            $confTab = array();

            foreach ($configs as $config) {
                $this->confTab[$config->key] = decrypt($config->value);
            }

            $_SESSION['configuration'] = serialize($this->confTab);
        } else {
            $this->confTab = unserialize($_SESSION['configuration']);
        }
    }

    /**
     * Get configuration value from it key
     * #### Example
     * ```php
     * global $conf; // global var, contain configurations
     * echo $conf->get('myConfigKey'); // print myConfigKey value
     * ```.
     * @param string configuration key
     * @param string configuration namespace (default is 'conf')
     *
     * @return string valeur de la configuration
     */
    public function get($key)
    {
        return isset($this->confTab[$key]) ? $this->confTab[$key] : '';
    }

    /**
     * Update or insert configuration value in database with specified key
     * #### Example
     * ```php
     * global $conf; // global var, contain configurations
     * echo $conf->put('myNewConfigKey','hello!'); //create configuration myNewConfigKey with value 'hello!'
     * echo $conf->put('myNewConfigKey','hello 2!'); //update configuration myNewConfigKey with value 'hello2!'
     * ```.
     *
     * @param string configuration key
     * @param string configuration value
     * @param string configuration namespace (default is 'conf')
     */
    public function put($key, $value)
    {
        $secured_value = crypt($value);
        $configurationManager = new self();
        if (isset($this->confTab[$key])) {
            $configurationManager->change(array('value' => $secured_value), array('key' => $key));
        } else {
            $configurationManager->add($key, $secured_value);
        }
        $this->confTab[$key] = $value;
        unset($_SESSION['configuration']);
    }

    /**
     * Remove configuration value in database with specified key
     * #### Example
     * ```php
     * global $conf; // global var, contain configurations
     * echo $conf->remove('myNewConfigKey'); //delete myNewConfigKey from 'conf' default namespace 
     * echo $conf->remove('myNewConfigKey','myCustomPluginConfig'); //delete myNewConfigKey from 'myCustomPluginConfig' namespace
     * ```.
     *
     * @param string configuration key
     * @param string configuration namespace (default is 'conf')
     */
    public function add($key, $value)
    {
        $config = new self();
        $config->key = $key;
        $config->value = $value;
        $config->save();
        $this->confTab[$key] = $value;
        unset($_SESSION['configuration']);
    }


}
