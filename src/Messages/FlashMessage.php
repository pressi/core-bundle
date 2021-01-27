<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Messages;


class FlashMessage
{
    private string $sessionKey = 'iido-message';



    public function set( string $message, string $id ): void
    {
        $_SESSION[ $this->sessionKey ][ $id ] = $message;
    }



    public function puke( string $id ): string|null
    {
        $message = null;

        if (isset($_SESSION[ $this->sessionKey ][ $id ]))
        {
            $message = $_SESSION[ $this->sessionKey ][ $id ];
            unset($_SESSION[ $this->sessionKey ][ $id ]);
        }

        return $message;
    }
}
