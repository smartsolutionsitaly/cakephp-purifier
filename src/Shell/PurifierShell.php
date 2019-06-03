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

namespace SmartSolutionsItaly\CakePHP\Purifier\Shell;

use Cake\Console\Shell;
use Cake\Utility\Inflector;
use SmartSolutionsItaly\CakePHP\Purifier\Purifier;

/**
 * Purifier shell.
 * @package SmartSolutionsItaly\CakePHP\Purifier\Shell
 * @author Lucio Benini <dev@smartsolutions.it>
 * @since 1.0.0
 */
class PurifierShell extends Shell
{
    /**
     * The main shell command.
     * @return null
     */
    public function main()
    {
        $this->info('HTMLPurifier');
        $this->info(__('Version: {0}.', Purifier::getPurifierVersion()));

        return null;
    }

    /**
     * Purifies a model in the datasource.
     * @param string $table The table name.
     * @param string $field The field.
     * @return null
     */
    public function model(string $table, string $field)
    {
        $table = $this->loadModel(Inflector::camelize($table));

        if ($table->hasField($field)) {
            $query = $table->hasBehavior('Translate') ? $table->find('translations') : $table->find();
            $purifier = Purifier::fromEmptyConfig();

            foreach ($query as $entity) {
                $entity->set($field, $purifier->purify($entity->get($field)));

                if (!empty($entity['_translations'])) {
                    foreach ($entity['_translations'] as $translation) {
                        $translation->set($field, $purifier->purify($translation->get($field)));
                    }
                }

                $table->save($entity);
            }

            $this->info(__('Done.'));
        }

        return null;
    }

    /**
     * Gets the option parser instance and configures it.
     * @return \Cake\Console\ConsoleOptionParser The option parser instance.
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('model', [
            'help' => __("Purifies a model in the datasource."),
            'parser' => [
                'description' => [
                    __("Purifies a model in the datasource.")
                ],
                'arguments' => [
                    'table' => [
                        'help' => __('The table.'),
                        'required' => true
                    ],
                    'field' => [
                        'help' => __('The field.'),
                        'required' => true
                    ]
                ]
            ]
        ]);

        return $parser;
    }
}
