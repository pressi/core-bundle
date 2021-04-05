<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Controller\Frontend;


use Contao\CoreBundle\Util\SymlinkUtil;
use Contao\Dbafs;
use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;
use IIDO\CoreBundle\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/_themeDesigner", defaults={"_scope": "frontend", "_token_check": false})
 */
class AjaxController extends AbstractController
{
    /**
     * @Route("/update", name=AjaxController::class)
     */
    public function updateAction( Request $request ): Response
    {
        $success = false;
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;

        if( $request->isXmlHttpRequest() )
        {
            $success = $this->updateField( $request->request->get('fieldName'), $request->request->get('fieldValue'), $request->request->get('page') );
        }

        if( $success )
        {
            $status = Response::HTTP_OK;
        }

        return new Response(json_encode(['success'=>$success]), $status, ['content-type'=>'application/json']);
    }



    /**
     * @Route("/updateFileManager", name="iido.core.themeDesigner.update_file_manager")
     */
    public function updateFileManagerAction( Request $request ): Response
    {
        $success = false;
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $returnJSON = [];

        if( $request->isXmlHttpRequest() )
        {
            $value = $request->request->get('fieldValue');
            $objFile = FilesModel::findByPath( $value );

            if( $objFile )
            {
                if( Validator::isStringUuid( $objFile->uuid ) )
                {
                    $value = StringUtil::uuidToBin( $objFile->uuid );
                }
                elseif( Validator::isBinaryUuid( $objFile->uuid) )
                {
                    $value = $objFile->uuid; //StringUtil::binToUuid( $objFile->uuid );
                }
                else
                {
                    $value = $objFile->id;
                }

                $returnJSON['value'] = Validator::isBinaryUuid( $objFile->uuid ) ? StringUtil::binToUuid( $objFile->uuid ) : $objFile->uuid;

                $success = $this->updateField( $request->request->get('fieldName'), $value, $request->request->get('page') );
            }
        }

        if( $success )
        {
            $status = Response::HTTP_OK;
        }

        $returnJSON = array_merge(['success'=>$success], $returnJSON);

        return new Response(json_encode($returnJSON), $status, ['content-type'=>'application/json']);
    }



    /**
     * @Route("/upload", name="iido.core.themeDesigner.upload")
     */
    public function uploadAction( Request $request ): Response
    {
        $success = false;
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $uploader = System::getContainer()->get('iido.core.service.file_uploader');

        $fieldName = $request->request->get('field');
        $pageId = $request->request->get('page');
        $file = $request->files->get( $fieldName );
        /* @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */

        if( !$file )
        {
            $file = $request->files->get('file');
        }

        $objRootPage = PageModel::findByPk( $pageId );

        if( !empty($file) )
        {
            if( false !== strpos(strtolower($fieldName), 'logo') )
            {
                $filesPath = sprintf('files/%s/Uploads/Logos', $objRootPage->alias);
                $dirPath = BundleConfig::getRootDir( true ) . $filesPath;

                if( !is_dir( $dirPath) )
                {
                    mkdir( $dirPath );
                }

                $fileName = $file->getClientOriginalName();

                if( file_exists($dirPath . DIRECTORY_SEPARATOR . $fileName) )
                {
                    $fileParts = explode('.', $fileName);
                    $extension = array_pop( $fileParts );

                    $fileName = implode('.', $fileParts) . '_' . substr(md5(openssl_random_pseudo_bytes(20)),-4) . '.' . $extension;
                }

//                $uploader->upload( $dirPath, $file, $fileName );
                $uploader->uploadToThemeDesigner( $dirPath, $file, $fileName, $fieldName, $pageId );

                $objFile = FilesModel::findByPath( $dirPath . $fileName );
                $success = true;
            }
        }

        if( $success )
        {
            $status = Response::HTTP_OK;
        }

        return new Response(json_encode(['success'=>$success]), $status, ['content-type'=>'application/json']);
    }



    protected function updateField( $fieldName, $fieldValue, $page )
    {
        $success = false;
//        $name = $request->request->get('fieldName');
//        $value = $request->request->get('fieldValue');
//        $pageId = $request->request->get('page');
        $name = $fieldName;
        $value = $fieldValue;
        $pageId = $page;

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(ThemeDesignerEntity::class);

        $model = $repository->findOneBy(['page'=>$pageId]);

        $func = 'set' . ucfirst( str_replace(' ', '', ucwords(str_replace('_', ' ', $name)) ) );

        if( $value === 'true' )
        {
            $value = '1';
        }
        else if( $value === 'false' )
        {
            $value = '';
        }

        if( !$model )
        {
            $success = true;

            $themeDesigner = new ThemeDesignerEntity();
            $themeDesigner->setTstamp( time() );
            $themeDesigner->setPage( $pageId );
            $themeDesigner->$func( $value );

            $entityManager->persist( $themeDesigner );
            $entityManager->flush();
        }
        else
        {
            $success = true;

            $model->$func( $value );

            $entityManager->persist( $model );
            $entityManager->flush();
        }

        return $success;
    }
}
