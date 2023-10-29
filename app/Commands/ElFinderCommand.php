<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ElFinderCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CodeIgniter';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'command:name';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // $volumeId = 'l1_';
        // // WRITEPATH . '/uploads/'
        // if (!empty($params))
        //     $path = $params[0];
        // else
        //     // $path = WRITEPATH . '/uploads/'; // without root path
        //     $path = '/uploads/'; // without root path

        // //$path = 'path\\to\\target'; // use \ on windows server
        // $hash = $volumeId . rtrim(strtr(base64_encode($path), '+/=', '-_.'), '.');

        // CLI::write(CLI::color("Path: " . $path . "\nHash: " . $hash, 'green'));
    }
}
