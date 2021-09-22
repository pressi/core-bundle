<?php
/*******************************************************************
 *
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 *
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *
 *******************************************************************/

namespace IIDO\CoreBundle\Permission;


use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Model;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use IIDO\CoreBundle\Config\IIDOConfig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class BackendPermissionChecker implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;


    private Connection $db;


    private TokenStorageInterface $tokenStorage;


    private IIDOConfig $config;



    /**
     * PermissionChecker constructor.
     *
     * @param Connection            $db
     * @param TokenStorageInterface $tokenStorage
     * @param IIDOConfig            $config
     */
    public function __construct( Connection $db, TokenStorageInterface $tokenStorage, IIDOConfig $config )
    {
        $this->db = $db;
        $this->tokenStorage = $tokenStorage;

        $this->config = $config;
    }



    public function hasFullAccessTo( string $strTable, string $fieldName, bool|Model $model = false, string $modelFieldName = '' ): bool
    {
        $accessFieldName = $fieldsFieldName = $tableClassName = false;

        switch( $strTable )
        {
            case "article":
            case "articles":
            case "tl_article":
                $accessFieldName    = 'includeArticleFields';
                $fieldsFieldName    = 'articleFields';
                $tableClassName     = ArticleModel::class;
                break;

            case "element":
            case "content":
            case "elements":
            case "contents":
            case "tl_content":
                $accessFieldName    = 'includeElementFields';
                $fieldsFieldName    = 'elementFields';
                $tableClassName     = ContentModel::class;
                break;
        }

        if( $accessFieldName )
        {
            $arrFields  = StringUtil::deserialize( $this->config->get( $fieldsFieldName ), true);

            if( $this->config->get( $accessFieldName ) && in_array( $fieldName, $arrFields) )
            {
                if( $model )
                {
                    if( !$model instanceof $tableClassName )
                    {
                        $model = $tableClassName::findByPk( $model );
                    }

                    if( $model->$modelFieldName )
                    {
                        return true;
                    }
                }
                else
                {
                    return true;
                }
            }
        }

        return false;
    }

}
