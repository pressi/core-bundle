<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Dca;


use Contao\Config;
use Contao\Controller;


class Field
{
    protected $name;


    protected $type         = 'text';
    protected $internType   = 'text';

    protected $noType       = false;
    protected $includeLabel = false;

    protected $update       = false;
    protected $dontAddDefaultSaveCallback = false;


    protected $withoutSQL   = false;


    protected $arrSQL       = array
    (
//        'alias'         => "varchar(##MAXLENGTH##) COLLATE utf8mb4_bin NOT NULL default ''",
        'alias'         => ['type'=>'binary', 'length'=>128, 'default'=>''],

        'text'          => "varchar(##MAXLENGTH##) NOT NULL default ''",
        'textarea'      => "mediumtext NULL",
        'checkbox'      => "char(1) NOT NULL default '##DEFAULT##'",
        'select'        => "varchar(##MAXLENGTH##) NOT NULL default '##DEFAULT##'",
        'fileTree'      => "binary(16) NULL",
        'pageTree'      => "int(10) unsigned NOT NULL default '0'",
        'imageSize'     => "varchar(64) NOT NULL default ''",
        'trbl'          => "varchar(128) NOT NULL default ''",
        'radioTable'    => "varchar(12) NOT NULL default ''",
        'radio'         => "varchar(12) NOT NULL default '##DEFAULT##'",

        'unit'          => "varchar(64) NOT NULL default ''",
        'inputUnit'     => "varchar(64) NOT NULL default ''",
        'headline'      => "varchar(255) NOT NULL default ''",

        'color'         => "varchar(64) NOT NULL default ''",
        'colorpicker'   => "varchar(64) NOT NULL default ''",

        'checkboxWizard'    => "blob NULL",
        'multiColumnWizard' => "blob NULL",
        'multiColumnEditor' => "blob NULL",

        'multiFileTree'     => "blob NULL"
    );


    protected $arrConfig        = array();
    protected $arrRemovedConfig = array();

    protected $arrEval      = array
    (
        'tl_class'  => 'w50'
    );

    protected $strTable;
    protected $strLangTable;

    protected $fieldPrefix  = 'iido([A-Za-z0-9]{0,})_';

    protected $dontUseRTE   = false;

    protected $isSelector   = false;

    protected $addedToSelector  = false;
    protected $selector         = '';

    protected $subpalettePosition       = '';
    protected $subpaletteReplaceField   = '';

    protected $objTable     = null;



    protected $fieldIsCopy      = false;
    protected $copyFromTable    = '';
    protected $copyFieldName    = '';

    protected $arrEvalOverride  = [];

    protected $alternativeOptionLangName    = '';
    protected $alternativeLabelLangName     = '';

    protected $label = '';
    protected $labelPrefix = '';



    /**
     * Default Table Listener
     *
     * @var string
     */
    protected $defaultTableListener     = 'iido.core.table.default';



    public function __construct( $strName, $strType = 'text', $withoutSQL = false, $strTable = '')
    {
        $this->name = $strName;
        $this->type = $strType;
        $this->internType = $strType;

        $this->withoutSQL = $withoutSQL;

        if( $strTable )
        {
            $this->strTable = $strTable;
        }

        if( $strType === 'textarea' )
        {
            $this->addEval('tl_class', 'clr', true);
        }
        elseif( $strType === 'image' || $strType === 'downloads' )
        {
            $this->type = 'fileTree';
        }
        elseif( $strType === 'images' )
        {
            $this->type = 'multiFileTree';
        }
        elseif( $strType === 'multiColumnEditor' )
        {
            $this->addEval('tl_class', 'clr', true);
        }

        return $this;
    }



    public static function create( $strName, $strType =  'text', $withoutSQL = false, $strTable = '' )
    {
        return new static($strName, $strType, $withoutSQL, $strTable);
    }



    public static function copy( $strName, $fromTable, $fromField = '' )
    {
        Controller::loadDataContainer( $fromTable );
        $arrConfig = $GLOBALS['TL_DCA'][ $fromTable ]['fields'][ $fromField?:$strName ];

        $objField = new static($strName, $arrConfig['inputType']);

        $objField->fieldIsCopy      = true;
        $objField->copyFromTable    = $fromTable;
        $objField->copyFieldName    = $fromField?:$strName;

//        $objField->addLabel( $arrConfig['label'] );
        if( isset($arrConfig['eval']) && is_array($arrConfig['eval']) && count($arrConfig['eval']) )
        {
            foreach( $arrConfig['eval'] as $eKey => $eValue )
            {
                $objField->addEval( $eKey, $eValue );
            }
        }

        $objField->addSQL( $arrConfig['sql'] );

        if( is_array($arrConfig) && count($arrConfig) )
        {
            foreach($arrConfig as $cKey => $cValue )
            {
                if( in_array($cKey, ['eval', 'inputType', 'sql']) )
                {
                    continue;
                }

                $objField->addConfig( $cKey, $cValue );
            }
        }

        return $objField;
    }



    public static function update( $strName, $objTable )
    {
        $objField = $objTable->getField( $strName );

        if( is_array($objField) )
        {
            $arrField = $objField;
            $objField = self::create( $strName, $objField['inputType'], !isset($objField['sql']), $objTable->getTableName() );

            if( isset($arrField['eval']) && is_array($arrField['eval']) && count($arrField['eval']) )
            {
                foreach( $arrField['eval'] as $key => $value )
                {
                    $objField->addEval( $key, $value );
                }
            }

            foreach($arrField as $strKey => $strValue )
            {
                if( in_array($strKey, ['label', 'inputType', 'exclude', 'eval', 'sql']) )
                {
                    continue;
                }

                $objField->addConfig( $strKey, $strValue );
            }

            $objField->setTable( $objTable );
        }

        return $objField;
    }



    public function setTable( $objTable )
    {
        $this->objTable = $objTable;
    }


    public function __set($name, $value)
    {
        $this->arrConfig[ $name ] = $value;
    }


    public function __get($name)
    {
        $this->arrConfig[ $name ];
    }



    public function setUseRTE( $useRTE = true )
    {
        $this->dontUseRTE = !$useRTE;

        return $this;
    }




    public function addConfig( $name, $value ): self
    {
        $this->arrConfig[ $name ] = $value;

        return $this;
    }



    public function removeConfig( $name ): self
    {
        unset( $this->arrConfig[ $name ] );

        if( !in_array($name, $this->arrRemovedConfig) )
        {
            $this->arrRemovedConfig[] = $name;
        }

        return $this;
    }



    public function addRegex( $regex ): self
    {
        $this->arrEval['rgxp'] = $regex;

        return $this;
    }



    public function addEval( $name, $value, $override = false ): self
    {
        if( $name === 'tl_class' && $override )
        {
            $this->arrEval[ $name ] = $value;
        }
        elseif( $name === "tl_class" && !$override )
        {
            $this->arrEval[ $name ] = trim($this->arrEval[ $name ] . ' ' . $value);
        }
        else
        {
            $this->arrEval[ $name ] = $value;
        }

        if( $override )
        {
            $this->arrEvalOverride[] = 'tl_class';
        }

        return $this;
    }



    public function getEval( $name = '')
    {
        if( $name )
        {
            return $this->arrEval[ $name ]??'';
        }

        return $this->arrEval;
    }



    public function setFieldPrefix( $prefix )
    {
        $this->fieldPrefix = $prefix;
    }



    public function setLangTable( $strLangTableName )
    {
        if( false === strpos($strLangTableName, 'tl_') )
        {
            $strLangTableName = 'tl_' . $strLangTableName;
        }

        $this->strLangTable = $strLangTableName;

        return $this;
    }



    public function isSelector()
    {
        if( isset($this->arrEval['submitOnChange']) && $this->arrEval['submitOnChange'] && $this->isSelector )
        {
            return true;
        }

        return false;
    }



    public function addDefault( $default )
    {
        $this->arrConfig['default'] = $default;

        return $this;
    }




    public function getDefault()
    {
        return $this->arrConfig['default']??'';
    }



    public function getName()
    {
        return $this->name;
    }



    public function setWithoutSQL( $withoutSQL )
    {
        $this->withoutSQL = $withoutSQL;
        return $this;
    }



    /**
     * @param string $strTable
     *
     * @throws \Exception
     */
    public function createDca( $strTable = '', $returnAsArray = false )
    {
        if( $strTable )
        {
            $this->strTable = $strTable;
        }

        if( !$this->strTable && !$returnAsArray )
        {
            throw new \Exception('Tabelle nicht konfiguriert, ein Feld "' . $this->name . ' (' . $this->type . ')" muss einer Tabelle zugewiesen werden!');
        }

        Controller::loadLanguageFile( $this->strTable );

        if( $this->strLangTable )
        {
            Controller::loadLanguageFile( $this->strLangTable );
        }

        $SQLType = $this->type;

//        switch( $this->type )
//        {
//            case "text":
//                $this->setDefaultTextFieldConfig();
//                break;
//
//            case "alias":
//                $this->setDefaultAliasFieldConfig();
//                break;
//
//            case "textarea":
//                $this->setDefaultTextareaFieldConfig();
//                break;
//
//            case "select":
//                $this->setDefaultSelectFieldConfig();
//                break;
//        }

        $typeFunction = 'setDefault' . ucfirst( $this->type ) . 'FieldConfig';

        if( method_exists($this, $typeFunction) )
        {
            $this->$typeFunction();
        }

        $arrFieldConfig = array
        (
            'label'         => $this->renderLabel(),
            'inputType'     => preg_replace('/^multiFile/', 'file', $this->type),
            'exclude'       => $this->arrConfig['exclude']??true,
            'eval'          => $this->arrEval,
        );

        if( $this->noType )
        {
            if( !$this->includeLabel )
            {
                unset( $arrFieldConfig['label'] );
            }
            unset( $arrFieldConfig['inputType'] );
            unset( $arrFieldConfig['exclude'] );
            unset( $arrFieldConfig['eval'] );
        }

        foreach($this->arrConfig as $key => $value)
        {
            $arrFieldConfig[ $key ] = $value;
        }

        $multiple = $this->arrEval['multiple'] ?? false;

        if( ($this->type === "select" || $this->type === "radio" || $this->type === "checkboxWizard"|| $this->type === "layoutWizard" || ($this->type === 'checkbox' && $multiple)) && !isset($arrFieldConfig['options']) )
        {
            $options = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ]['options'][ $this->alternativeOptionLangName?:$this->name ]??[];

            if($options && count($options) )
            {
                $arrFieldConfig['options'] = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ]['options'][ $this->alternativeOptionLangName?:$this->name ];
            }

            if( !isset($arrFieldConfig['options']) )
            {
                $arrFieldConfig['options'] = [];
            }

            if( !is_array($arrFieldConfig['options']) || !count($arrFieldConfig['options']) )
            {
                $arrFieldConfig['options'] = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ]['options_' . trim($this->alternativeOptionLangName?:$this->name) ]??[];
            }
        }

        if( ($this->type === "select" || $this->type === "radio" || $this->type === "checkboxWizard" || ($this->type === 'checkbox' && $multiple)) )
        {
            $fieldKeyName = '';

            if( !isset($arrFieldConfig['options']) )
            {
                if( $arrFieldConfig['options'][0] === 'field' )
                {
                    $fieldKeyName = $arrFieldConfig['options'][1];

                    $arrFieldConfig['options'] = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ]['options'][ $this->alternativeOptionLangName?:$fieldKeyName ];
                }
            }

            if( !is_array($arrFieldConfig['options']) || !count($arrFieldConfig['options']) )
            {
                $optionsLang = $this->alternativeOptionLangName?:$fieldKeyName;

                if( $optionsLang )
                {
                    $arrFieldConfig['options'] = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ]['options_' . $optionsLang ]??[];
                }
            }
        }

//        echo "<pre>";
//        print_r( $this->name );
//        echo "<br>";
//        print_r( $arrFieldConfig );
//        exit;

//        if( $this->type === "checkboxWizard" )
//        {
//            echo "<pre>";
//            print_r( $this->name );
//
//            echo "<br>";
//            print_r( $this->strLangTable );
//
//            echo "<br>";
//            print_r( $this->strTable );
//
//            echo "<br>";
//            print_r( $arrFieldConfig );
//
//            echo "<br>";
//            print_r( $GLOBALS['TL_LANG'][ $this->strTable ] );
//            exit;
//
//            echo "</pre>";
//        }


        if( !$this->withoutSQL )
        {
            if( !$this->update )
            {
                $strSQL = isset($this->arrSQL[ $SQLType ]) ? ($this->arrSQL[ $SQLType ] ? : $this->arrSQL[ $this->type ]) : '';

                if( $this->type === "checkbox" && $multiple )
                {
                    if( $arrFieldConfig['default'] )
                    {
                        $arrFieldConfig['sql']      = "varchar(50) DEFAULT '" . $arrFieldConfig['default'] . "'";
//                        $arrFieldConfig['sql']      = "text NULL";
//                        echo "<pre>"; print_r( $arrFieldConfig ); exit;
                    }
                    else
                    {
                        $arrFieldConfig['sql']      = "blob NULL";
                    }
//                    $arrFieldConfig['relation'] = array('type'=>'hasMany', 'load'=>'lazy');
                }
                else
                {
                    if( $strSQL )
                    {
                        $strSQL = str_replace('##MAXLENGTH##', $this->getEval('maxlength'), $strSQL);

                        $arrFieldConfig['sql'] = str_replace('##DEFAULT##', $this->getDefault(), $strSQL);
                    }
                }
            }
            else
            {
                $arrFieldConfig['sql'] = $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->name ]['sql'];
            }
        }
//echo "<pre>"; print_r( $this->name ); echo "<br>"; print_r( $arrFieldConfig ); echo "<br>"; print_r( $this->arrConfig ); echo "</pre>";
//if( $arrFieldConfig['inputType'] === 'checkbox' && $arrFieldConfig['eval']['multiple'] )
//{
//    echo "<pre>"; print_r( $arrFieldConfig ); exit;
//}

        if( $this->isSelector() )
        {

            if( !in_array($this->getName(), $GLOBALS['TL_DCA'][ $this->strTable ]['palettes']['__selector__']) )
            {
                $GLOBALS['TL_DCA'][ $this->strTable ]['palettes']['__selector__'][] = $this->getName();
            }
        }

        if( $this->isAddedToSelector() )
        {
            $subPalette = $GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $this->selector ]??'';

            if( !str_contains($subPalette, $this->getName()) )
            {
                if( 'end' === $this->subpalettePosition )
                {
                    $GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $this->selector ] = $subPalette . (strlen($subPalette) ? ',' : '') . $this->getName();
                }
            }
        }

        if( $returnAsArray )
        {
            return $arrFieldConfig;
        }
        else
        {
            $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->name ] = $arrFieldConfig;
        }
    }



    public function getConfigAsArray()
    {
        return $this->createDca('', true);
    }



    protected function setDefaultTextFieldConfig()
    {
        if( !$this->getEval('maxlength') )
        {
            $this->addEval('maxlength', 255);
        }

        if( $this->issetEval('decodeEntities') )
        {
            $this->addEval('decodeEntities', true);
        }
    }



    protected function setDefaultCheckboxFieldConfig()
    {
        $this->addEval('tl_class', 'w50 m12');
    }



    protected function setDefaultAliasFieldConfig()
    {
        $this->type = 'text';

        $this->addEval('rgxp', 'alias');
        $this->addEval('maxlength', 128);
        $this->addEval('doNotCopy', true);

        if( !$this->dontAddDefaultSaveCallback )
        {
            $this->addSaveCallback($this->getTableListener($this->strTable), 'generateAlias');
        }
    }



    protected function setDefaultSelectFieldConfig()
    {
        if( !$this->getEval('maxlength') )
        {
            $this->addEval('maxlength', 32);
        }
    }



    protected function setDefaultColorFieldConfig()
    {
        $this->setDefaultColorpickerFieldConfig();
    }



    protected function setDefaultColorpickerFieldConfig()
    {
        $this->type = 'text';

        $this->addEval('maxlength', 64);
        $this->addEval('multiple', true);
        $this->addEval('size', 2);
        $this->addEval('colorpicker', true);
        $this->addEval('isHexColor', true);
        $this->addEval('decodeEntities', true);
        $this->addEval('tl_class', 'wizard');
    }



    protected function setDefaultUrlFieldConfig()
    {
        $this->type = 'text';

        $this->addEval('rgxp', 'url');
        $this->addEval('decodeEntities', true);
        $this->addEval('dcaPicker', true);
        $this->addEval('tl_class', 'wizard');
        $this->addEval('maxlength', 255);
    }



    protected function setDefaultInputUnitFieldConfig()
    {
        $this->setDefaultUnitFieldConfig();
    }



    protected function setDefaultUnitFieldConfig()
    {
        $this->type = 'inputUnit';

        $this->addEval('includeBlankOption', true);
        $this->addEval('maxlength', 20);
        $this->addEval('tl_class', 'w50');
        $this->addEval('rgxp', 'digit_auto_inherit');

        $this->addConfig('options', $GLOBALS['TL_CSS_UNITS']);
    }



    protected function setDefaultHeadlineFieldConfig()
    {
        $this->type = 'inputUnit';

        $this->addEval('maxlength', 200);
        $this->addEval('tl_class', 'w50 clr');

        $this->addConfig('options', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);
    }



//    protected function setDefaultMultiColumnEditorFieldConfig()
//    {
//        $this->addEval('tl_class', 'clr', true);
//    }



    protected function setDefaultFileTreeFieldConfig()
    {
        if( !$this->issetEval('fieldType') )
        {
            $this->addEval('fieldType', 'radio');
        }

        if( !$this->issetEval('filesOnly') )
        {
            $this->addEval('filesOnly', true);
        }

        if( $this->getEval('isDownloads') && (!$this->issetEval('extensions') || !$this->getEval('extensions')) )
        {
            $this->addEval('extensions', \Config::get('allowedDownload'));
        }

        if( !$this->issetEval('extensions') || !$this->getEval('extensions') )
        {
            $this->addEval('extensions', \Config::get('validImageTypes'));
        }

        $useBlob = false;

        if( $this->issetEval('files') && !$this->getEval('files') )
        {
            $useBlob = true;
        }

        if( $this->getEval('isGallery') || $this->getEval('isDownloads') || $this->getEval('multiple') || $useBlob )
        {
            $this->addSQL("blob NULL");
        }

        if( $this->internType === 'downloads' )
        {
            $this->addEval('multiple', true);
            $this->addEval('fieldType', 'checkbox');
            $this->addEval('filesOnly', true);
            $this->addEval('isDownloads', true);
            $this->addEval('extensions', Config::get('allowedDownload'));
            $this->addEval('orderField', 'order' . ucfirst($this->name));

            $this->addSQL("blob NULL");
        }
    }



    protected function setDefaultMultiFileTreeFieldConfig()
    {
        if( !$this->issetEval('fieldType') )
        {
            $this->addEval('fieldType', 'checkbox');
        }

        if( !$this->issetEval('multiple') )
        {
            $this->addEval('multiple', true);
        }

        if( !$this->issetEval('orderField') && ($this->issetEval('noOrderField') && !$this->getEval('noOrderField')) )
        {
            $this->addEval('orderField', 'order' . ucfirst($this->name) );
        }

        $this->addEval('extensions', \Config::get('validImageTypes'));
        $this->addEval('isGallery', true);
        $this->addEval('files', true);

        $this->addEval('tl_class', 'clr', true);

        $this->addSQL("blob NULL");
    }



    protected function setDefaultImageSizeFieldConfig()
    {
        $this->addConfig('reference', $GLOBALS['TL_LANG']['MSC']);

        if( $this->name !== "bgSize" )
        {
            $this->addConfig('options_callback', function ()
            {
                return \System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(\BackendUser::getInstance());
            });
        }
        else
        {
            $arrOptions = $GLOBALS['TL_LANG'][ $this->strTable ]['options'][ $this->name ]??[];

            if( !is_array($arrOptions) || !count($arrOptions) )
            {
                $arrOptions = $GLOBALS['TL_LANG'][ $this->strTable ]['options_' . $this->name ]??[];
            }

            $this->addConfig('options', $arrOptions );
        }

        $this->addEval('rgxp', ($this->name === "bgSize" ? 'extnd' : 'natural'));
        $this->addEval('includeBlankOption', true);
        $this->addEval('nospace', true);
        $this->addEval('helpwizard', true);
    }



    protected function setDefaultTrblFieldConfig()
    {
        $this->addConfig('options', $GLOBALS['TL_CSS_UNITS']);

        $this->addEval('includeBlankOption', true);
    }



    protected function setDefaultPageTreeFieldConfig()
    {
        $this->addConfig('foreignKey', 'tl_page.title');

        if( !in_array('relation', $this->arrRemovedConfig) && !key_exists('relation', $this->arrConfig) )
        {
            $this->addConfig('relation', array('type'=>'hasOne', 'load'=>'eager'));
        }

        $this->addEval('fieldType', 'radio');

        if( !in_array('tl_class', $this->arrEvalOverride) )
        {
            $this->addEval('tl_class', 'clr');
        }
    }



    protected function setDefaultTextareaFieldConfig()
    {
        Controller::loadDataContainer('tl_content');

        $arrConfig = $GLOBALS['TL_DCA']['tl_content']['fields']['text'];

        foreach( $arrConfig as $key => $value )
        {
            if( in_array($key, array('label', 'exclude', 'inputType', 'sql', 'search')) || $this->dontUseRTE && $key === 'explanation' )
            {
                continue;
            }

            if( $key === 'eval' )
            {
                foreach( $value as $evalName => $evalValue)
                {
                    if( $evalName === 'mandatory' )
                    {
                        $evalValue = false;
                    }

                    if( $this->dontUseRTE && $evalName === 'rte' )
                    {
                        continue;
                    }

                    $this->addEval($evalName, $evalValue);
                }
            }
            else
            {
                $this->arrConfig[ $key ] = $value;
            }
        }

        if( $this->dontUseRTE )
        {
            $this->addEval('decodeEntities', true);
            $this->addEval('style', 'height:60px');
        }
    }



    protected function setDefaultExplanationFieldConfig()
    {
        $this->type = 'explanation';

        $this->addEval('text', '');
        $this->addEval('class', 'tl_info');
        $this->addEval('tl_class', 'long');
    }



    public function addSaveCallback( $listenerClass, $functionName)
    {
//        if( !$this->dontAddDefaultSaveCallback )
//        {
            if( !isset($this->arrConfig['save_callback']) )
            {
                $this->arrConfig['save_callback'] = array();
            }

            $addCallback = true;

            foreach( $this->arrConfig['save_callback'] as $callback )
            {
                if( $callback[0] === $listenerClass && $callback[1] === $functionName )
                {
                    $addCallback = false;
                }
            }

            if( $addCallback )
            {
                $this->arrConfig['save_callback'][] = array( $listenerClass, $functionName );
            }
//        }
    }



    protected function getTableListener( $strTable )
    {
        return $this->objTable->getTableListener()?:$this->defaultTableListener;
    }



    protected function issetEval( $name )
    {
        if( isset($this->arrEval[ $name ]) )
        {
            return true;
        }

        return false;
    }



    /**
     * Render Field Label
     *
     * @return mixed
     */
    protected function renderLabel()
    {
        if( $this->label )
        {
            return $this->label;
        }


        $fieldName = $this->name;

        if( $this->fieldPrefix )
        {
            $fieldName = preg_replace('/^' . $this->fieldPrefix . '/', '', $fieldName);
        }

        if( $this->alternativeLabelLangName )
        {
            $fieldName = $this->alternativeLabelLangName;
        }

        $strLabel = $GLOBALS['TL_LANG'][ $this->strLangTable?:$this->strTable ][ $fieldName ]??'';

        if( !$strLabel )
        {
            $strLabel = $GLOBALS['TL_LANG']['DEF'][ $fieldName ]??'';
        }

        if( $this->labelPrefix )
        {
            if( is_array($strLabel) )
            {
                $strLabel[0] = $this->labelPrefix . $strLabel[0];
            }
            else
            {
                $strLabel = $this->labelPrefix . $strLabel;
            }
        }

        return $strLabel;
    }


    /**
     * @param Table|ExistTable $objTable
     *
     * @return self
     */
    public function addFieldToTable( $objTable )
    {
        $objTable->addFields( [ $this ] );

        return $this;
    }



    public function addToTable( ExistTable|Table $objTable ): self
    {
        $this->setTable( $objTable );

        if( $this->isAddedToSelector() )
        {
            $objTable->addFieldToSubpalette($this->getName(), $this->selector, $this->subpalettePosition, $this->subpaletteReplaceField);
        }

        return $this->addFieldToTable( $objTable );
    }



    public function addSQL( $sql )
    {
        $this->arrSQL[ $this->type ] = $sql;

        return $this;
    }



    public function setSelector( $selector = true )
    {
        return $this->setAsSelector( $selector );
    }



    public function setAsSelector( $selector = true )
    {
        if( $selector )
        {
            $this->isSelector = true;
            $this->addEval('submitOnChange', true);

            $this->addEval('tl_class', 'clr subfields');
        }
        else
        {
            $this->isSelector = false;
            $this->addEval('submitOnChange', false);
        }

        return $this;
    }



    public function addToSearch( $addToSearch = true )
    {
        $this->search = $addToSearch;

        $this->arrConfig['search'] = $addToSearch;

        return $this;
    }



    public function addToFilter( $addToFilter = true )
    {
        $this->filter = $addToFilter;

        $this->arrConfig['filter'] = $addToFilter;

        return $this;
    }



    public function addOptions( $arrOptions )
    {
        $this->arrConfig['options'] = $arrOptions;

        return $this;
    }



    public function addOptionsName( $optionName )
    {
        $this->alternativeOptionLangName = $optionName;

        return $this;
    }



    public function addLabelName( $labelName )
    {
        $this->alternativeLabelLangName = $labelName;

        return $this;
    }



    public function setLabel( $strLabel )
    {
        $this->label = $strLabel;
        return $this;
    }



    public function setLabelPrefix( $strLabelPrefix )
    {
        $this->labelPrefix = $strLabelPrefix;
        return $this;
    }



    public function setNoType( $setNoType )
    {
        $this->noType = $setNoType;

        return $this;
    }



    public function addToSelector( $strSelector, $position = 'end', $replaceField = '' ): self
    {
        $this->addedToSelector  = true;
        $this->selector         = $strSelector;

        $this->subpalettePosition       = $position;
        $this->subpaletteReplaceField   = $replaceField;

        return $this;
    }



    public function addToSubpalette( $strSubpalette, $position = 'end', $replaceField = '' ): self
    {
        return $this->addToSelector( $strSubpalette, $position, $replaceField );
    }



    public function isAddedToSelector(): bool
    {
        return $this->addedToSelector;
    }



    public function setAddToSelector( bool $addToSelector ): void
    {
        $this->addedToSelector  = $addToSelector;
    }



    public function getSelector(): string
    {
        return $this->selector;
    }



    public function updateField()
    {
        $this->update = true;
        $this->objTable->overrideField( $this->name, $this );
    }



    public function dontAddDefaultSaveCallback( $dontAdd = true )
    {
        $this->dontAddDefaultSaveCallback = $dontAdd;

        return $this;
    }
}
