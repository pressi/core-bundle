<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\DataContainer;


use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\DataContainer;
use Contao\Date;
use Contao\Environment;
use Contao\Files;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;


/**
 * Provide methods to edit the local configuration file.
 */
class DC_YamlConfigFile extends DataContainer implements \editable
{
    public function __construct( string $strTable )
	{
	    parent::__construct();

		$this->intId = Input::get('id');

		// Check whether the table is defined
		if( !$strTable || !isset($GLOBALS['TL_DCA'][ $strTable ]) )
		{
			$logger = static::getContainer()->get('monolog.logger.contao');
		    $logger->log(\Psr\Log\LogLevel::ERROR, 'Could not load data container configuration for "' . $strTable . '"', array('contao' => new ContaoContext(__METHOD__, TL_ERROR)));

			trigger_error('Could not load data container configuration', E_USER_ERROR);
		}

		// Build object from global configuration array
		$this->strTable = $strTable;

		WebsiteSettingsUtil::checkIfConfigFileExists();

		// Call onload_callback (e.g. to check permissions)
		if( \is_array($GLOBALS['TL_DCA'][ $this->strTable ]['config']['onload_callback']) )
		{
			foreach( $GLOBALS['TL_DCA'][ $this->strTable ]['config']['onload_callback'] as $callback )
			{
				if( \is_array($callback) )
				{
					$this->import( $callback[0] );
					$this->{$callback[0]}->{$callback[1]}( $this );
				}
				elseif( \is_callable($callback) )
				{
					$callback( $this );
				}
			}
		}
	}



	public function create(): string
	{
		return $this->edit();
	}



	public function cut(): string
	{
		return $this->edit();
	}



	public function copy(): string
	{
		return $this->edit();
	}



	public function move(): string
	{
		return $this->edit();
	}



	public function edit(): string
	{
		$return = '';
		$ajaxId = null;

		if( Environment::get('isAjaxRequest') )
		{
			$ajaxId = func_get_arg(1);
		}

		// Build an array from boxes and rows
		$this->strPalette = $this->getPalette();
		$boxes = StringUtil::trimsplit(';', $this->strPalette);
		$legends = array();

		if( !empty($boxes) )
		{
			foreach( $boxes as $k => $v )
			{
			    $boxes[ $k ] = StringUtil::trimsplit(',', $v);

				foreach( $boxes[ $k ] as $kk => $vv )
				{
					if( preg_match('/^\[.*]$/', $vv) )
					{
						continue;
					}

					if( preg_match('/^{.*}$/', $vv) )
					{
                        $legends[ $k ] = substr($vv, 1, -1);
						unset($boxes[ $k ][ $kk ]);
					}
//					elseif( !\is_array($GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $vv ]) || $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $vv ]['exclude'] )
                    elseif( !\is_array($GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $vv ]) )
					{
                        unset($boxes[ $k ][ $kk ]);
					}

                    $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $vv ]['exclude'] = false;
				}

				// Unset a box if it does not contain any fields
				if( empty($boxes[ $k ]) )
				{
				    unset($boxes[ $k ]);
				}
			}

			/** @var AttributeBagInterface $objSessionBag */
			$objSessionBag = System::getContainer()->get('session')->getBag('contao_backend');

			// Render boxes
			$class = 'tl_tbox';
			$fs = $objSessionBag->get('fieldset_states');

//			$strBoxes = '';
//			foreach( $boxes as $kboxes => $vboxes )
//            {
//                $strBoxes .= $kboxes . ':' . implode(',', $vboxes);
//            }
//            $this->log($strBoxes, __METHOD__, TL_ERROR);
//            $this->log($this->strPalette, __METHOD__, TL_ERROR);

			foreach( $boxes as $k => $v )
			{
				$strAjax = '';
				$blnAjax = false;
				$key = '';
				$cls = '';
				$legend = '';

				if( isset($legends[ $k ]) )
				{
					list($key, $cls) = explode(':', $legends[$k]);
					$legend = "\n" . '<legend onclick="AjaxRequest.toggleFieldset(this, \'' . $key . '\', \'' . $this->strTable . '\')">' . ($GLOBALS['TL_LANG'][ $this->strTable ][ $key ] ?? $key) . '</legend>';
				}

				if (isset($fs[ $this->strTable ][ $key ]))
				{
					$class .= ($fs[ $this->strTable ][ $key ] ? '' : ' collapsed');
				}
				else
				{
					$class .= (($cls && $legend) ? ' ' . $cls : '');
				}

				$return .= "\n\n" . '<fieldset' . ($key ? ' id="pal_' . $key . '"' : '') . ' class="' . $class . ($legend ? '' : ' nolegend') . '">' . $legend;

				// Build rows of the current box
				foreach( $v as $vv )
				{

					if ($vv == '[EOF]')
					{
					    if( $blnAjax && Environment::get('isAjaxRequest') )
						{
							return $strAjax . '<input type="hidden" name="FORM_FIELDS[]" value="' . StringUtil::specialchars($this->strPalette) . '">';
						}

						$blnAjax = false;
						$return .= "\n  " . '</div>';

						continue;
					}

					if( preg_match('/^\[.*]$/', $vv) )
					{
						$thisId = 'sub_' . substr($vv, 1, -1);
						$blnAjax = ($ajaxId == $thisId && Environment::get('isAjaxRequest'));
						$return .= "\n  " . '<div id="' . $thisId . '" class="subpal cf">';

						continue;
					}

					$this->strField = $vv;
					$this->strInputName = $vv;
                    $this->varValue = WebsiteSettingsUtil::getWebsiteSettings( $this->strField, $this->strTable );

					// Handle entities
					if( $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ]['inputType'] == 'text' || $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ]['inputType'] == 'textarea' )
					{
						if( $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ]['eval']['multiple'] )
						{
							$this->varValue = StringUtil::deserialize( $this->varValue );
						}

						if( !\is_array($this->varValue) )
						{
							$this->varValue = htmlspecialchars($this->varValue);
						}
						else
						{
							foreach( $this->varValue as $key => $val )
							{
								$this->varValue[ $key ] = htmlspecialchars($val);
							}
						}
					}

					// Call load_callback
					if( \is_array($GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ]['load_callback']) )
					{
						foreach( $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ]['load_callback'] as $callback )
						{
							if( \is_array($callback) )
							{
								$this->import( $callback[0] );
								$this->varValue = $this->{$callback[0]}->{$callback[1]}( $this->varValue, $this );
							}
							elseif( \is_callable($callback) )
							{
								$this->varValue = $callback( $this->varValue, $this );
							}
						}
					}

					// Build row
					$blnAjax ? $strAjax .= $this->row() : $return .= $this->row();
				}

				$class = 'tl_box';
				$return .= "\n" . '</fieldset>';
			}
		}

		$this->import(Files::class, 'Files');
		$filePath = WebsiteSettingsUtil::getConfigFilePath( true );

		// Check whether the target file is writeable
		if( !$this->Files->is_writeable( $filePath ) )
		{
			Message::addError( sprintf($GLOBALS['TL_LANG']['ERR']['notWriteable'], $filePath) );
		}

		// Submit buttons
		$arrButtons = [];
		$arrButtons['save'] = '<button type="submit" name="save" id="save" class="tl_submit" accesskey="s">' . $GLOBALS['TL_LANG']['MSC']['save'] . '</button>';
		$arrButtons['saveNclose'] = '<button type="submit" name="saveNclose" id="saveNclose" class="tl_submit" accesskey="c">' . $GLOBALS['TL_LANG']['MSC']['saveNclose'] . '</button>';

		// Call the buttons_callback (see #4691)
		if( \is_array($GLOBALS['TL_DCA'][$this->strTable]['edit']['buttons_callback']) )
		{
			foreach( $GLOBALS['TL_DCA'][$this->strTable]['edit']['buttons_callback'] as $callback )
			{
				if( \is_array($callback) )
				{
					$this->import($callback[0]);
					$arrButtons = $this->{$callback[0]}->{$callback[1]}( $arrButtons, $this );
				}
				elseif( \is_callable($callback) )
				{
					$arrButtons = $callback( $arrButtons, $this );
				}
			}
		}

		// Add the buttons and end the form
		$return .= '
</div>
<div class="tl_formbody_submit">
<div class="tl_submit_container">
  ' . implode(' ', $arrButtons) . '
</div>
</div>
</form>';

		// Begin the form (-> DO NOT CHANGE THIS ORDER -> this way the onsubmit attribute of the form can be changed by a field)
		$return = Message::generate() . ($this->noReload ? '
<p class="tl_error">' . $GLOBALS['TL_LANG']['ERR']['general'] . '</p>' : '') . '
<div id="tl_buttons">
<a href="' . $this->getReferer(true) . '" class="header_back" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']) . '" accesskey="b" onclick="Backend.getScrollOffset()">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a>
</div>
<form id="' . $this->strTable . '" class="tl_form tl_edit_form" method="post"' . (!empty($this->onsubmit) ? ' onsubmit="' . implode(' ', $this->onsubmit) . '"' : '') . '>
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="' . $this->strTable . '">
<input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">
<input type="hidden" name="FORM_FIELDS[]" value="' . StringUtil::specialchars($this->strPalette) . '">' . $return;

		// Reload the page to prevent _POST variables from being sent twice
		if( !$this->noReload && Input::post('FORM_SUBMIT') == $this->strTable )
		{
			// Call onsubmit_callback
			if( \is_array($GLOBALS['TL_DCA'][$this->strTable]['config']['onsubmit_callback']) )
			{
				foreach( $GLOBALS['TL_DCA'][$this->strTable]['config']['onsubmit_callback'] as $callback )
				{
					if( \is_array($callback) )
					{
						$this->import( $callback[0] );
						$this->{$callback[0]}->{$callback[1]}( $this );
					}
					elseif( \is_callable($callback) )
					{
						$callback($this);
					}
				}
			}

			// Reload
			if( isset($_POST['saveNclose']) )
			{
				Message::reset();
				$this->redirect($this->getReferer());
			}

			$this->reload();
		}

		// Set the focus if there is an error
		if( $this->noReload )
		{
			$return .= '
<script>
  window.addEvent(\'domready\', function() {
    Backend.vScrollTo(($(\'' . $this->strTable . '\').getElement(\'label.error\').getPosition().y - 20));
  });
</script>';
		}

		return $return;
	}



	/**
	 * Save the current value
	 *
	 * @param mixed $varValue
	 */
	protected function save($varValue): void
	{
		if (Input::post('FORM_SUBMIT') != $this->strTable)
		{
			return;
		}

		$arrData = $GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $this->strField ];

		// Make sure that checkbox values are boolean
		if ($arrData['inputType'] == 'checkbox' && !$arrData['eval']['multiple'])
		{
			$varValue = $varValue ? true : false;
		}

		if( $varValue )
		{
			// Convert binary UUIDs (see #6893)
			if ($arrData['inputType'] == 'fileTree')
			{
				$varValue = StringUtil::deserialize($varValue);

				if (!\is_array($varValue))
				{
					$varValue = StringUtil::binToUuid($varValue);
				}
				else
				{
					$varValue = serialize(array_map('StringUtil::binToUuid', $varValue));
				}
			}

			// Convert date formats into timestamps
			if ($varValue !== null && $varValue !== '' && \in_array($arrData['eval']['rgxp'], array('date', 'time', 'datim')))
			{
				$objDate = new Date($varValue, Date::getFormatFromRgxp($arrData['eval']['rgxp']));
				$varValue = $objDate->tstamp;
			}

			// Handle entities
			if ($arrData['inputType'] == 'text' || $arrData['inputType'] == 'textarea')
			{
				$varValue = StringUtil::deserialize($varValue);

				if (!\is_array($varValue))
				{
					$varValue = StringUtil::restoreBasicEntities($varValue);
				}
				else
				{
					$varValue = serialize(array_map('StringUtil::restoreBasicEntities', $varValue));
				}
			}
		}

		// Trigger the save_callback
		if (\is_array($arrData['save_callback']))
		{
			foreach ($arrData['save_callback'] as $callback)
			{
				if (\is_array($callback))
				{
					$this->import($callback[0]);
					$varValue = $this->{$callback[0]}->{$callback[1]}($varValue, $this);
				}
				elseif (\is_callable($callback))
				{
					$varValue = $callback($varValue, $this);
				}
			}
		}

		$strCurrent = $this->varValue;

		// Handle arrays and strings
		if (\is_array($strCurrent))
		{
			$strCurrent = serialize($strCurrent);
		}
		elseif (\is_string($strCurrent))
		{
			$strCurrent = html_entity_decode($this->varValue, ENT_QUOTES, Config::get('characterSet'));
		}

        // Save the value if there was no error
		if( $strCurrent != $varValue && (\strlen($varValue) || !$arrData['eval']['doNotSaveEmpty']) )
		{
		    WebsiteSettingsUtil::updateWebsiteSetting( $this->strField, $this->strTable, $varValue );

			$deserialize = StringUtil::deserialize($varValue);
			$fileValue = WebsiteSettingsUtil::getWebsiteSettings( $this->strField, $this->strTable );
			$prior = \is_bool($fileValue) ? ($fileValue ? 'true' : 'false') : $fileValue;

			// Add a log entry
			if( !\is_array($deserialize) && !\is_array(StringUtil::deserialize($prior)) )
			{
				if ($arrData['inputType'] == 'password' || $arrData['inputType'] == 'textStore')
				{
					$logger = static::getContainer()->get('monolog.logger.contao');
                    $logger->log(\Psr\Log\LogLevel::INFO, 'The iido core configuration (settings) variable "' . $this->strField . '" has been changed', array('contao' => new ContaoContext(__METHOD__, TL_CONFIGURATION)));
				}
				else
				{
                    $logger = static::getContainer()->get('monolog.logger.contao');
                    $logger->log(\Psr\Log\LogLevel::INFO, 'The iido core configuration (settings) variable "' . $this->strField . '" has been changed from "' . $prior . '" to "' . $varValue . '"', array('contao' => new ContaoContext(__METHOD__, TL_CONFIGURATION)));
				}
			}

			// Set the new value so the input field can show it
			$this->varValue = $deserialize;
            WebsiteSettingsUtil::updateWebsiteSetting( $this->strField, $this->strTable, $deserialize );
		}
	}



	public function getPalette(): string
	{
	    $palette = 'default';
		$strPalette = $GLOBALS['TL_DCA'][ $this->strTable ]['palettes'][ $palette ];

		// Check whether there are selector fields
		if (!empty($GLOBALS['TL_DCA'][$this->strTable]['palettes']['__selector__']))
		{
			$sValues = array();
			$subpalettes = array();

			foreach ($GLOBALS['TL_DCA'][$this->strTable]['palettes']['__selector__'] as $name)
			{
			    $trigger = WebsiteSettingsUtil::getWebsiteSettings( $name, $this->strTable );

				// Overwrite the trigger if the page is not reloaded
				if (Input::post('FORM_SUBMIT') == $this->strTable)
				{
					$key = (Input::get('act') == 'editAll') ? $name . '_' . $this->intId : $name;

					if( !$GLOBALS['TL_DCA'][ $this->strTable ]['fields'][ $name ]['eval']['submitOnChange'] )
					{
						$trigger = Input::post($key);
					}
				}

				if( $trigger )
				{
					if( $GLOBALS['TL_DCA'][$this->strTable]['fields'][$name]['inputType'] == 'checkbox' && !$GLOBALS['TL_DCA'][$this->strTable]['fields'][$name]['eval']['multiple'] )
					{
						$sValues[] = $name;

						// Look for a subpalette
						if (isset($GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $name ]))
						{
							$subpalettes[ $name ] = $GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $name ];
						}
					}
					else
					{
						$sValues[] = $trigger;
						$key = $name . '_' . $trigger;

						// Look for a subpalette
						if( isset($GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $key ]) )
						{
							$subpalettes[ $name ] = $GLOBALS['TL_DCA'][ $this->strTable ]['subpalettes'][ $key ];
						}
					}
				}
			}

			// Build possible palette names from the selector values
			if (empty($sValues))
			{
				$names = array('default');
			}
			elseif (\count($sValues) > 1)
			{
				$names = $this->combiner($sValues);
			}
			else
			{
				$names = array($sValues[0]);
			}

			// Get an existing palette
			foreach ($names as $paletteName)
			{
				if (isset($GLOBALS['TL_DCA'][$this->strTable]['palettes'][$paletteName]))
				{
					$strPalette = $GLOBALS['TL_DCA'][$this->strTable]['palettes'][$paletteName];
					break;
				}
			}

			// Include subpalettes
			foreach ($subpalettes as $k=>$v)
			{
				$strPalette = preg_replace('/\b' . preg_quote($k, '/') . '\b/i', $k . ',[' . $k . '],' . $v . ',[EOF]', $strPalette);
			}
		}

		return $strPalette;
	}
}

class_alias(DC_YamlConfigFile::class, 'DC_YamlConfigFile');
