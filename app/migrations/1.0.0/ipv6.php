<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class Ipv6Migration_100
 */
class Ipv6Migration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('ipv6', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_BIGINTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'ip_from',
                        [
                            'type' => 'ip6',
                            'notNull' => true,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'ip_to',
                        [
                            'type' => 'ip6',
                            'notNull' => true,
                            'after' => 'ip_from'
                        ]
                    ),
                    new Column(
                        'country_code',
                        [
                            'type' => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'ip_to'
                        ]
                    ),
                    new Column(
                        'country_name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 64,
                            'after' => 'country_code'
                        ]
                    ),
                    new Column(
                        'region_name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 128,
                            'after' => 'country_name'
                        ]
                    ),
                    new Column(
                        'city_name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 128,
                            'after' => 'region_name'
                        ]
                    ),
                    new Column(
                        'latitude',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'city_name'
                        ]
                    ),
                    new Column(
                        'longitude',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'latitude'
                        ]
                    ),
                    new Column(
                        'zip_code',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 30,
                            'after' => 'longitude'
                        ]
                    ),
                    new Column(
                        'time_zone',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 8,
                            'after' => 'zip_code'
                        ]
                    )
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        //self::$_connection->execute('ALTER TABLE ipv6 ALTER COLUMN ip_to TYPE ip6 USING ip_to::ip6');
        //self::$_connection->execute('ALTER TABLE ipv6 ALTER COLUMN ip_from TYPE ip6 USING ip_to::ip6');
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
