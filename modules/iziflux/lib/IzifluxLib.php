<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

if (! defined('_PS_VERSION_')) {
    exit();
}

/**
 * Class to retrieve base64_encode values
 */
class IzifluxLib extends ObjectModel
{

    /**
     * Encode base_64, we absolutely need it for communication with Iziflux
     */
    public static function base64encode($var)
    {
        return base64_encode($var);
    }
}
