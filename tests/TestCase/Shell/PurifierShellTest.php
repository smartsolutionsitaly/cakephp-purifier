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

namespace SmartSolutionsItaly\CakePHP\Purifier\Test\TestCase\Purifier;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use SmartSolutionsItaly\CakePHP\Purifier\Shell\PurifierShell;

/**
 * SmartSolutionsItaly\CakePHP\Purifier\PurifierShell Test Case
 */
class PurifierShellTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var PurifierShell
     */
    public $Purifier;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->Purifier = new PurifierShell($this->io);
        $this->Purifier->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Purifier);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->assertNull($this->exec('purifier'));
        $this->assertOutputContains(\SmartSolutionsItaly\CakePHP\Purifier\Purifier::getPurifierVersion());
    }

    /**
     * Test model method
     *
     * @return void
     */
    public function testModel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     */
    public function testGetOptionParser()
    {
        $this->assertNotNull($this->Purifier->getOptionParser());
    }
}
