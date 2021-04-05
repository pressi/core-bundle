<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Twig;


use Contao\Controller;
use Contao\StringUtil;
use Contao\Validator;
use IIDO\BasicBundle\Helper\BasicHelper;
use IIDO\CoreBundle\Config\BundleConfig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class TwigTemplatesExtension extends AbstractExtension
{
    /**
     * get Twig Template Filter
     *
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return array
        (
            new TwigFilter('language', [$this, 'languageFilter']),
            new TwigFilter('renderId', [$this, 'renderIdFilter']),
            new TwigFilter('renderScriptVar', [$this, 'renderScriptVarFilter']),
            new TwigFilter('checkUUID', [$this, 'checkdUUIDFilter'])
//            new TwigFilter('price', array($this, 'priceFilter')),
//            new TwigFilter('replaceNL', array($this, 'replaceNewLineFilter')),
//            new TwigFilter('masterStylesheetExists', array($this, 'checkIfMasterStylesheetExists')),
//            new TwigFilter('masterTemplateExists', array($this, 'checkIfMasterTemplateExists'))
        );
    }



    /**
     * get Twig Template Functions
     *
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return array
        (
            new TwigFunction('websiteIcon', [$this, 'getWebsiteIcon'], ['is_safe' => ['html']]),

            new TwigFunction('getLayout', [$this, 'getLayout'], ['is_safe' => ['html']]),
            new TwigFunction('renderLayoutChooser', [$this, 'renderLayoutChooser'], ['is_safe' => ['html']])
//            new TwigFunction('websiteTitle', array($this, 'getWebsiteTitleFunction')),
//            new TwigFunction('socialmedia', array($this, 'getSocialmediaFunction')),
//            new TwigFunction('ua', array($this, 'getUAFunctions')),
//            new TwigFunction('lang', array($this, 'getLanguageFunctions')),
//
//            new TwigFunction('getTrans', [$this, 'getTrans']),
//            new TwigFunction('renderClass', [$this, 'renderClass']),
//            new TwigFunction('generateRoute', [$this, 'generateRoute'])
        );
    }



    public function languageFilter( $path )
    {
        $parts  = StringUtil::trimsplit('.', $path);
        $lang   = $GLOBALS['TL_LANG'];

        foreach( $parts as $part )
        {
            if( is_array($lang) )
            {
                $lang = $lang[ $part ];
            }
        }

        if( !is_array($lang) )
        {
            return $lang;
        }

        return '';
    }



    public function checkdUUIDFilter( $uuidBinary )
    {
        if( Validator::isBinaryUuid( $uuidBinary ) )
        {
            $uuidBinary = StringUtil::binToUuid( $uuidBinary );
        }

        return $uuidBinary;
    }



    public function renderIdFilter( $id )
    {
        if( preg_match('/\_/', $id) )
        {
            $parts = StringUtil::trimsplit('_', $id);
            $id = implode('-', $parts);
        }
        else
        {
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $id,$matches);

            if( is_array($matches) && count($matches[0]) )
            {
                $id = implode('-', $matches[0]);
            }
        }

        return $id;
    }



    public function renderScriptVarFilter( $variable )
    {
        if( preg_match('/\_/', $variable) )
        {
            $parts = StringUtil::trimsplit('_', $variable);

            $variable = '';
            foreach( $parts as $part )
            {
                $variable .= ucfirst( $part );
            }
        }

        return ucfirst( $variable );
    }



    public function getWebsiteIcon( $icon )
    {
        return file_get_contents( BundleConfig::getRootDir( true ) . BundleConfig::getBundlePath( true ). '/images/icons/' . $icon );
    }



    public function getLayout( $name, $value )
    {
        Controller::loadLanguageFile('iido');

        $bundlePath = BundleConfig::getBundlePath( true, false );
        $content = '<div class="layout-container">';

        if( !$value )
        {
            $value = 'layout01';
        }

        $content .= '<div class="layout-image"><img src="' . $bundlePath . '/images/layouts/header/' . $value . '.png"></div>';
        $content .= '<div class="layout-label"><span>' . $GLOBALS['TL_LANG']['IIDO']['layouts']['header'][ $value ] . '</span></div>';

        return $content . '</div>';
    }



    public function renderLayoutChooser( $name, $label, $value )
    {
        if( !$value )
        {
            $value = 'layout01';
        }

        $bundlePath = BundleConfig::getBundlePath( true, false );

        $content = '<div class="layout-item' . ($value === $name ? ' active' : '') . '"><div class="layout-item-inside">';

        $content .= '<div class="layout-image"><img src="' . $bundlePath . '/images/layouts/header/' . $name . '.png"></div>';
        $content .= '<div class="layout-label">' . $label . '</div>';

        $content .= '<div class="layout-button"><button data-value="' . $name . '">Aktivieren</button><div class="text">AKTIV</div></div>';

        return $content . '</div></div>';
    }



//    public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
//    {
//        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
//        $price = '$'.$price;
//
//        return $price;
//    }
//
//
//
//    public function replaceNewLineFilter( $text )
//    {
//        return \Controller::replaceInsertTags( str_replace(array('{{br}}','\n','$lt;br&gt;'), array('<br>','<br>','<br>'), $text) );
//    }
//
//
//
////    public function checkIfMasterStylesheetExists( $text )
////    {
////        $rootDir    = dirname(\System::getContainer()->getParameter('kernel.project_dir'));
////        $fileName   = trim( strtolower( preg_replace(array('/ \/ /', '/ \(HTC\)/'), array('/', '.htc'), $text) ) );
////
////        if( !preg_match('/.htc$/', $fileName) )
////        {
////            $fileName = $fileName . '.css';
////        }
////
////        if( file_exists($rootDir . '/files/master/css/' . $fileName) )
////        {
////            $text = '<span class"file-exists" style="text-decoration:line-through">' . $text . '</span>';
////        }
////
////        return $text;
////    }
//
////    public function checkIfMasterTemplateExists( $text )
////    {
////        $file       = preg_replace(array('/\s\(([A-Za-z0-9\s\-]{1,})\)/'), array(''), $text);
////        $fileName   = trim( strtolower( preg_replace(array('/ \/ /'), array('/'), $file) ) );
////
////        $rootDir        = dirname(\System::getContainer()->getParameter('kernel.project_dir'));
////        $arrTemplates   = $this->getTemplateFolders();
////        $websiteDir     = 'global';
////
////        if( count($arrTemplates) === 1 )
////        {
////            $websiteDir = $arrTemplates[0];
////
////            if( file_exists($rootDir . '/templates/' . $websiteDir . '/' . $fileName) )
////            {
////                $text = '<span class"file-exists" style="text-decoration:line-through">' . $text . '</span>';
////            }
////        }
////        elseif( count( $arrTemplates ) > 1 )
////        {
////            $text = $text . ' <span class="exists-folder">' . implode(",", $arrTemplates) . '</span>';
////        }
////
////        return $text;
////    }
//
//
//
//    protected function getTemplateFolders()
//    {
//        return scan( BasicHelper::getRootDir() .  '/templates' );
//    }
//
//
//
//    public function getWebsiteTitleFunction()
//    {
//        global $objPage;
//        return $GLOBALS['TL_CONFIG']['websiteTitle']?:$objPage->rootTitle;
//    }
//
//
//
//    public function getSocialmediaFunction()
//    {
////        $objTemplate    = new \FrontendTemplate('ce_iido_socialmedia');
////        $objTemplate->links = array(); //\IIDO\WebsiteBundle\Helper\Socialmedia::getSocialmediaLinks();
//
////        if( count($objTemplate->links) > 0 )
////        {
////            return $objTemplate->getResponse();
////        }
//
//        return '';
////        return \Controller::replaceInsertTags('{{iido::socialmedia::icons}}');
//    }
//
//    public function getUAFunctions()
//    {
//        return \Environment::get('agent')->class;
//    }
//
//
//
//    public function getLanguageFunctions()
//    {
//        return BasicHelper::getLanguage();
//    }
//
//
//
//    public function getName()
//    {
//        return 'app_extension';
//    }
//
//
//
//
//    public function getTrans( $table, $field, $key )
//    {
//        Controller::loadLanguageFile( $table );
//        return $GLOBALS['TL_LANG'][ $table ]['options'][ $field ][ $key ];
//    }
//
//
//
//    public function renderClass( $strClass )
//    {
//        $strClass = preg_replace('/ /', '-', $strClass);
//
//        return $strClass;
//    }
//
//
//
//    public function generateRoute( $router, $routeName, $varName, $varValue )
//    {
//        return $router->generate($routeName, [$varName=>$varValue]);
//    }
}
