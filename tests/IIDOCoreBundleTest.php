<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Tests;


use IIDO\CoreBundle\IIDOCoreBundle;
use PHPUnit\Framework\TestCase;


/**
 * Test the Contao IIDO Core Bundle.
 *
 * @author Stephan Preßl <development@prestep.at>
 */
class IIDOCoreBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new IIDOCoreBundle();

        $this->assertInstanceOf('IIDO\CoreBundle\IIDOCoreBundle', $bundle);
    }
}
