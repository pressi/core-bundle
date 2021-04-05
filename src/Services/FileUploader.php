<?php


namespace IIDO\CoreBundle\Services;


use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Dbafs;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{

    public function upload( $uploadDir, UploadedFile $file, $filename )
    {
        try
        {
            $strFile = $file->move( $uploadDir, $filename );
            $fileParts = explode('/files', $strFile->getPath());

//            Dbafs::addResource( 'files' . $fileParts[1] . '/' . $strFile->getFilename() );
//            Dbafs::updateFolderHashes('files' . $fileParts[1]);

//            $webDir = StringUtil::stripRootDir(System::getContainer()->getParameter('contao.web_dir'));
//            $rootDir = BundleConfig::getRootDir( true );
//            SymlinkUtil::symlink( $rootDir . 'files', $webDir . DIRECTORY_SEPARATOR . 'files', $rootDir);
        }
        catch( FileException $e )
        {
            $logger = static::getContainer()->get('monolog.logger.contao');
            $logger->log(LogLevel::ERROR, "failed to upload image: " . $e->getMessage(), ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]);

            throw new FileException('Failed to upload file');
        }
    }



    public function uploadToThemeDesigner( $uploadDir, UploadedFile $file, $filename, $fieldName, $pageId )
    {
        $doctrine = System::getContainer()->get('doctrine');
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(ThemeDesignerEntity::class);

        $model = $repository->findOneBy(['page'=>$pageId]);
        $func = 'set' . ucfirst( str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)) ) );

        try
        {
            $strFile = $file->move( $uploadDir, $filename );
            $fileParts = explode('/files', $strFile->getPath());

            $file = Dbafs::addResource( 'files' . $fileParts[1] . '/' . $strFile->getFilename() );
            Dbafs::updateFolderHashes('files' . $fileParts[1]);

            $hash = $file->uuid;
            if( Validator::isStringUuid( $hash ) )
            {
                $hash = StringUtil::uuidToBin( $hash );
            }

            $model->$func( $hash );

            $entityManager->persist( $model );
            $entityManager->flush();

//            $webDir = StringUtil::stripRootDir(System::getContainer()->getParameter('contao.web_dir'));
//            $rootDir = BundleConfig::getRootDir( true );
//            SymlinkUtil::symlink( $rootDir . 'files', $webDir . DIRECTORY_SEPARATOR . 'files', $rootDir);
        }
        catch( FileException $e )
        {
            $logger = static::getContainer()->get('monolog.logger.contao');
            $logger->log(LogLevel::ERROR, "failed to upload image: " . $e->getMessage(), ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]);

            throw new FileException('Failed to upload file');
        }
    }
}
