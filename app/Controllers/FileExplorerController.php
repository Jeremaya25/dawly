<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use elFinder;
use elFinderConnector;

class FileExplorerController extends BaseController
{
    public function getFile()
    {
        $varName=current_url(true)->setSegment(1,'')->getPath();
        $file = new \CodeIgniter\Files\File(WRITEPATH . "/uploads/" . urldecode($varName));

        if (!$file->isFile()){     // if (!is_file(WRITEPATH . "/uploads/" . $varName)){
            throw new \CodeIgniter\Exceptions\PageNotFoundException($varName . ' no found');
        }

        // $filedata = readfile(WRITEPATH . "/uploads/" . $varName);
        $filedata = new \SplFileObject($file->getPathname(), "r");

        $data1 = $filedata->fread($filedata->getSize());

        return $this->response->setContentType($file->getMimeType())->setBody($data1);
    }

    public function manager()
    {
        $data['page_title'] = 'File explorer. elFinder';
        $data['title'] = 'File explorer. elFinder';
        $data['connector'] = base_url('/private/fileconnector');

        echo view('private/main_file', $data);
    }

    public function connector()
    {
        $opts = array(
            'debug' => true,
            'roots' => array(
                array(
                    'driver'        => 'LocalFileSystem',
                    'path'          => WRITEPATH . '/uploads',
                    'URL'           => base_url('/private/fileget/'),
                    // 'uploadDeny'    => array('all'),                  // All Mimetypes not allowed to upload
                    // 'uploadAllow'   => array('image', 'text/plain', 'application/pdf'), // Mimetype `image` and `text/plain` allowed to upload
                    // 'uploadOrder'   => array('deny', 'allow'),        // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => array($this, 'elfinderAccess'), // disable and hide dot starting files (OPTIONAL)
                    'alias'         => 'Documents',
                    'attributes' => array(
                        array(
                            'pattern' => '/\.zop$/', //You can also set permissions for file types by adding, for example, .jpg inside pattern.
                            'read'    => false,
                            'write'   => false,
                            'locked'  => true,
                            'hidden'  => true
                        ),
                        array(
                            'pattern' => '/personal.* /', //comença per la paraula pesonal (expressio regular entre barres)
                            'read'    => false,
                            'write'   => false,
                            'locked'  => true,
                            'hidden'  => false
                        )
                    ),
                    // more elFinder options here
                )
            ),
        );
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    public function elfinderAccess($attr, $path, $data, $volume, $isDir, $relpath)
    {

        /*function startsWith($haystack, $needle) {
            return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
        }
        function endsWith($haystack, $needle) {
            return substr_compare($haystack, $needle, -strlen($needle)) === 0;
        }*/


        $basename = basename($path);

        if (substr_compare($basename, '.html', -5) === 0)
            return !($attr == 'read' || $attr == 'write');
        else {

            return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
                && strlen($relpath) !== 1           // but with out volume root
                ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
                :  null;                                 // else elFinder decide it itself

        }
    }
}
