<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Ipv4 extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Identity
     * @Column(type="integer", nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $ip_from;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $ip_to;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=false)
     */
    public $country_code;

    /**
     *
     * @var string
     * @Column(type="string", length=64, nullable=false)
     */
    public $country_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=false)
     */
    public $region_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=false)
     */
    public $city_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $latitude;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $longitude;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=false)
     */
    public $zip_code;

    /**
     *
     * @var string
     * @Column(type="string", length=8, nullable=false)
     */
    public $time_zone;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'ipv4';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Ipv4[]|Ipv4
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Ipv4
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Модель по ip
     * метод find с запросом содержащий >>= не получится использовать, будет ошибка
     * поэтому используем fetchColumn
     *
     * @todo почитать как сделать Fetch_Column
     *
     * @param $ip
     * @return Ipv4 | null
     */
    public static function findByIp($ip)
    {
        $di = Phalcon\Di::getDefault();

        /** @var \Phalcon\Db\Adapter\Pdo\Postgresql $db */
        $db = $di->getShared('db');
        $result = $db->fetchOne("SELECT id FROM ipv4 WHERE ip4r(ip_from, ip_to) >>= :ip",
            Phalcon\Db::FETCH_ASSOC,
            [
                'ip' => $ip
            ]
        );

        return (isset($result['id'])) ? self::findFirstByid($result['id']) : null;
    }
}
