<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\CoreBundle\Event\FilterPageTypeEvent;
use Doctrine\DBAL\Connection;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;


/**
 * @ServiceTag("kernel.event_listener")
 */
class FilterPageTypeListener
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }



    public function __invoke( FilterPageTypeEvent $event ): void
    {
        $dc = $event->getDataContainer();

        if( !$dc->activeRecord )
        {
            return;
        }

        $parentType = $this->connection->fetchOne('SELECT type FROM tl_page WHERE id=?', [$dc->activeRecord->pid]);

        if( 'root' !== $parentType )
        {
            $event->removeOption( 'global_element' );

            return;
        }

        $siblingTypes = $this->connection->fetchFirstColumn(
            'SELECT DISTINCT(type) FROM tl_page WHERE pid=? AND id!=?',
            [$dc->activeRecord->pid, $dc->activeRecord->id]
        );

        foreach (array_intersect(['global_element'], $siblingTypes) as $type)
        {
            $event->removeOption( $type );
        }
    }

}
