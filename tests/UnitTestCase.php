<?php

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var bool
     */
    private $_loaded = false;



    public function setUp()
    {
        parent::setUp();

        // Загрузка дополнительных сервисов, которые могут потребоваться во время тестирования
        $di = Di::getDefault();

        // получаем любые компоненты DI, если у вас есть настройки, не забудьте передать их родителю
        $di->set('db', function () {
            // @todo сделать тестовый конфиг
            return new \Phalcon\Db\Adapter\Pdo\Postgresql([
                'host'        => 'localhost',
                'username'    => 'dmitriy',
                'password'    => '538970',
                'dbname'      => 'ip_project',
                'port'        => '5433'
            ]);
        });
        $this->di->set('modelsManager', function() {
            return new \Phalcon\Mvc\Model\Manager();
        });

        $this->di->set('modelsMetadata', function() {
            return new \Phalcon\Mvc\Model\Metadata\Memory();
        });

        $this->setDi($di);

        $this->_loaded = true;
    }

    /**
     * Проверка на то, что тест правильно настроен
     *
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        if (!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }
}