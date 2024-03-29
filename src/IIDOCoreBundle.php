<?php
declare(strict_types=1);

/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
//use IIDO\CoreBundle\DependencyInjection\IIDOCoreExtension;


/**
 * Configures the Contao IIDO Core Bundle.
 *
 * @author Stephan Preßl <development@prestep.at>
 */
class IIDOCoreBundle extends Bundle
{
    public function getContainerExtension()
    {
        if( null === $this->extension )
        {
            $this->extension = $this->createContainerExtension();
        }

        return $this->extension;
    }


    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
