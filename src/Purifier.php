<?php
/**
 * cakephp-purifier (https://github.com/smartsolutionsitaly/cakephp-purifier)
 * Copyright (c) 2019 Smart Solutions S.r.l. (https://smartsolutions.it)
 *
 * HTML Purifier for CakePHP
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  cakephp-plugin
 * @package   cakephp-purifier
 * @author    Lucio Benini <dev@smartsolutions.it>
 * @copyright 2019 Smart Solutions S.r.l. (https://smartsolutions.it)
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @link      https://smartsolutions.it Smart Solutions
 * @since     1.0.0
 */

namespace SmartSolutionsItaly\CakePHP\Purifier;

/**
 * HTML purifier.
 * @package SmartSolutionsItaly\CakePHP\Purifier
 * @author Lucio Benini <dev@smartsolutions.it>
 * @since 1.0.0
 */
class Purifier
{
    /**
     * Base purifier instance.
     * @var \HTMLPurifier
     */
    protected $_instance;

    /**
     * Purifier constructor.
     */
    public function __construct()
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('Core.EscapeNonASCIICharacters', true);
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('CSS.AllowedProperties', ['text-decoration', 'text-align']);
        $config->set('Attr.EnableID', false);
        $config->set('HTML.AllowedElements', [
            'i',
            'em',
            'blockquote',
            'div',
            'b',
            'p',
            'strong',
            'span',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'ol',
            'li',
            'sup',
            'sub'
        ]);
        $config->set('AutoFormat.RemoveEmpty', true);
        $this->_instance = new \HTMLPurifier($config);
    }

    /**
     * Gets the purifier version.
     * @return string The purifier version.
     * @see \HTMLPurifier::VERSION
     */
    public static function getPurifierVersion(): string
    {
        return \HTMLPurifier::VERSION;
    }

    /**
     * Creates an instance using the given configuration.
     * @param array $config An array of configuration values. Possible keys are "encoding", "doctype", "properties", "attributes" and "elements".
     * @return Purifier A Purifier instance.
     */
    public static function fromConfig(array $config = []): Purifier
    {
        $instance = new static;
        $properties = [
            'encoding' => 'Core.Encoding',
            'doctype' => 'HTML.Doctype',
            'properties' => 'CSS.AllowedProperties',
            'attributes' => 'Attr.EnableID',
            'elements' => 'HTML.AllowedElements'
        ];

        foreach ($properties as $property => $setting) {
            if (isset($config[$property])) {
                $instance->setConfig($setting, $config[$property]);
            }
        }

        return $instance;
    }

    /**
     * Sets the configuration.
     * @param string $key The key to set.
     * @param mixed|null $value The value to set.
     * @return Purifier The current instance.
     */
    public function setConfig(string $key, $value = null): Purifier
    {
        $this->_instance->config->set($key, $value);

        return $this;
    }

    /**
     * Creates an instance from the default configuration.
     * @return Purifier A Purifier instance.
     */
    public static function fromDefaultConfig(): Purifier
    {
        return new static;
    }

    /**
     * Creates an instance from an empty configuration.
     * @return Purifier A Purifier instance.
     */
    public static function fromEmptyConfig(): Purifier
    {
        $instance = new static;

        return $instance
            ->setConfig('CSS.AllowedProperties', [])
            ->setConfig('HTML.AllowedElements', []);
    }

    /**
     * Gets the purifier version.
     * @return string The purifier version.
     * @see \HTMLPurifier::$version
     */
    public function getInstaceVersion(): string
    {
        return $this->_instance->version;
    }

    /**
     * Gets the configuration.
     * @param string $key The key to get.
     * @param mixed|null $default The return value when the key does not exist.
     * @return mixed|null The configuration value.
     */
    public function getConfig(string $key, $default = null)
    {
        $value = $this->_instance->config->get($key);

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * Filters an HTML snippet/document to be XSS-free and standards-compliant.
     * @param string|array $data String of HTML to purify.
     * @return string|array Purified HTML.
     * @see \HTMLPurifier::purify()
     */
    public function purify($data)
    {
        if (is_array($data)) {
            $results = $this->_instance->purifyArray($data);

            foreach ($results as &$result) {
                $result = str_replace('&#160;', ' ', $result);
            }

            return $results;
        } else {
            return str_replace('&#160;', ' ', $this->_instance->purify($data));
        }
    }
}
