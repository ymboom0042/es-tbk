<?php
namespace EasySwoole\EasySwoole;

use App\Crontab\Item\GetTbItem;
use App\Crontab\Order\Twenty;
use App\Exception\Exception;
use App\Pool\RedisPool;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Utility\File;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        // 注册自定义错误异常 优先级小于控制器级别
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[Exception::class,'handle']);

        // 加载配置文件
        self::loadConf();


    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.

        // fast-cache
        Cache::getInstance()
            -> setTempDir(EASYSWOOLE_TEMP_DIR)
            -> attachToServer(ServerManager::getInstance() -> getSwooleServer());


        // 注册redis
        $config = new \EasySwoole\Pool\Config();
        $redisConfig = new RedisConfig(Config::getInstance() -> getConf('REDIS'));
        Manager::getInstance() -> register(new RedisPool($config, $redisConfig), 'redis');


        // 注册orm 读
        $wConfig = new \EasySwoole\ORM\Db\Config(Config::getInstance() -> getConf('MYSQL_WRITE'));
        DbManager::getInstance() -> addConnection(new Connection($wConfig), 'write');

        // 添加任务
//        self::addTask();
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }


    /**
     *  加载配置
     */
    public static function loadConf()
    {
        //遍历目录中的文件
        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App/Conf');

        if (is_array($files)) {
            //$files['files'] 一级目录下所有的文件,不包括文件夹
            foreach ($files['files'] as $file) {
                $fileNameArr = explode('.', $file);
                $fileSuffix  = end($fileNameArr);

                if ($fileSuffix == 'php') {

                    //引入之后,文件名自动转为小写,成为配置的key
                    Config::getInstance()->loadFile($file);
                }
            }
        }
    }


    /**
     * 任务
     */
    public static function addTask()
    {
        // 注册crontab任务
//        Crontab::getInstance() -> addTask(GetTbItem::class);
        Crontab::getInstance() -> addTask(Twenty::class);
    }
}