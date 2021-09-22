<?php

use IIDO\CoreBundle\Widget\InputUnitWidget;



/**
 * backend form fields
 */

$GLOBALS['BE_FFL']['inputUnit'] = InputUnitWidget::class;



/**
 * models
 */

$GLOBALS['TL_MODELS']['tl_iido_company'] = \IIDO\CoreBundle\Model\CompanyModel::class;
