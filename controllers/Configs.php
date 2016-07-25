<?php
namespace DEfr\VueManager\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Hoa\Websocket\Server as WebsocketServer;
use Hoa\Socket\Server as SocketServer;
use Hoa\Event\Bucket;

class Configs extends Controller
{
    protected $shPrefix = '';

    public $implement = [];

    public function __construct()
    {
        parent::__construct();
        $this->shPrefix = 'cd '.__dir__.' && ';
        BackendMenu::setContext('DEfr.VueManager', 'vuemanager', 'vue-configs');
    }

    public function index()
    {

        $this->vars['webpack']     = file_get_contents(dirname(__dir__).'/webpack.config.js');
        $this->vars['gulpfile']    = file_get_contents(dirname(__dir__).'/gulpfile.js');
        $this->vars['packageJson'] = file_get_contents(dirname(__dir__).'/package.json');

    }

    public function onNpmList()
    {

        $websocket = new WebsocketServer(
            new SocketServer('ws://127.0.0.1:9889')
        );
        $websocket->on('open', function (Bucket $bucket)
        {
            echo 'new connection', "\n";

            return;
        });
        $websocket->on('message', function (Bucket $bucket)
        {
            $data = $bucket->getData();
            echo '> message ', $data['message'], "\n";
            $bucket->getSource()->send($data['message']);
            echo '< echo', "\n";

            return;
        });
        $websocket->on('close', function (Bucket $bucket)
        {
            echo 'connection closed', "\n";

            return;
        });
        $websocket->run();
        $this->vars['stdout'] = shell_exec($this->shPrefix.'npm list');
    }

    public function onNpmInstall()
    {
        $this->vars['stdout'] = shell_exec($this->shPrefix.'npm install');
    }

    public function onNpmRunDev()
    {
        $this->vars['stdout'] = shell_exec($this->shPrefix.'npm run dev');
    }

    public function onWebpackSave()
    {
        }

    public function onGulpfileSave()
    {
        }

    public function onPackageJsonSave()
    {
        }

}
