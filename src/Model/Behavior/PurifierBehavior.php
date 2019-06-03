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

namespace SmartSolutionsItaly\CakePHP\Purifier\Behavior;

use App\Utility\Purifier;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;

/**
 * Purifier behavior.
 * @package SmartSolutionsItaly\CakePHP\Purifier\Model\Behavior
 * @author Lucio Benini <dev@smartsolutions.it>
 * @since 1.0.0
 */
class PurifierBehavior extends Behavior
{
    /**
     * Default configuration.
     * @var array
     */
    protected $_defaultConfig = [
        'priority' => 1
    ];

    /**
     * The Model.beforeSave event is fired before each entity is saved.
     * Stopping this event will abort the save operation.
     * When the event is stopped the result of the event will be returned.
     * @param Event $event The event.
     * @param EntityInterface $entity The entity.
     * @param ArrayObject $options Extra options.
     * @return bool A value indicating whether if the event has been validated.
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->purifyEntity($entity);
        $this->purifyTranslations($entity);

        return true;
    }

    /**
     * Purifies the given entity.
     * @param EntityInterface $entity The entity.
     * @return EntityInterface
     */
    protected function purifyEntity(EntityInterface $entity): EntityInterface
    {
        $fields = $this->getConfig('fields', []);

        if (!is_array($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field => $config) {
            if (!empty($entity->$field)) {
                if ($config === null) {
                    $purifier = Purifier::fromEmptyConfig();
                } else {
                    $purifier = Purifier::fromConfig(is_array($config) ? $config : [$config]);
                }

                $entity->set($field, $purifier->purify($entity->get($field)));
            }
        }

        return $entity;
    }

    /**
     * Purifies the translations of the given entity.
     * @param EntityInterface $entity The entity.
     * @return EntityInterface
     */
    protected function purifyTranslations(EntityInterface $entity): EntityInterface
    {
        if ($this->getTable()->hasBehavior('Translate') && !empty($entity->_translations)) {
            foreach (Configure::read('App.locales') as $locale => $name) {
                $this->purifyEntity($entity->translation($locale));
            }
        }

        return $entity;
    }
}
