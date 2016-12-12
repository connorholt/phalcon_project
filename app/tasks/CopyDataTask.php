<?php
use Phalcon\Cli\Task;

class CopyDataTask extends Task
{
    const IP_V4 = 4;
    const IP_V6 = 6;

    const FIELD_IP_FROM = 0;
    const FIELD_IP_TO = 1;
    const FIELD_COUNTRY_CODE = 2;
    const FIELD_COUNTRY_NAME = 3;
    const FIELD_REGION_NAME = 4;
    const FIELD_CITY_NAME = 5;
    const FIELD_LAT = 6;
    const FIELD_LONG = 7;
    const FIELD_ZIP_CODE = 8;
    const FIELD_TIME_ZONE = 9;

    const QUERY_COUNT = 1000;

    /**
     * Главный action
     *
     * @param array $params
     */
    public function mainAction(array $params = null)
    {
        if (!isset($params[0])) {
            echo 'Введите версию ip адресов 4 или 6' . PHP_EOL;
            exit;
        }

        $v = ($params[0] == self::IP_V4) ? self::IP_V4 : self::IP_V6;

        $db = $this->getDi()->getShared('db');

        try {
            $db->begin();
            $this->copyData($db, $v);
            $db->commit();
        } catch (Exception $e) {

            print_r($e->getMessage());
            $db->rollback();
        }
    }

    /**
     * Возвращает названия таблиц участвующих при копировании
     *
     * @param $v
     * @return array
     */
    private function getTablesName($v)
    {
        return ($v == self::IP_V4)
            ? ['ipv4', 'ipv4_tmp', 'ipv4_old']
            : ['ipv6', 'ipv6_tmp', 'ipv6_old'];
    }

    /**
     * Название файла
     *
     * @param $v
     * @return array
     */
    private function getFileName($v)
    {
        return ($v == self::IP_V4)
            ? 'IP2LOCATION-LITE-DB11.CSV'
            : 'IP2LOCATION-LITE-DB11.IPV6.CSV';
    }

    /**
     * Путь к файлам
     *
     * @return string
     */
    private function getPath()
    {
        return BASE_PATH
        . DIRECTORY_SEPARATOR
        . 'public'
        . DIRECTORY_SEPARATOR
        . 'files'
        . DIRECTORY_SEPARATOR;
    }

    /**
     * Процесс копирования ip адресов
     *
     * Создаем временную таблицу
     * Копируем туда все данные
     * Переименовываем старую таблицу
     * Временную переименовываем на текущую
     * Ставим сиквенс на новую таблицу
     * Удаляем старую таблицу
     *
     * Все это в транзакции
     *
     * @param $v
     * @param $db
     */
    private function copyData($db, $v)
    {
        /** @var \Phalcon\Db\Adapter\Pdo\Postgresql $db */

        list(
            $table,
            $tableTmp,
            $tableOld
            ) = $this->getTablesName($v);

        echo $table . PHP_EOL;
        echo $tableTmp . PHP_EOL;
        echo $tableOld . PHP_EOL;

        $db->execute("DROP TABLE IF EXISTS $tableTmp");
        $db->execute("CREATE TABLE $tableTmp (LIKE $table INCLUDING ALL)");

        $handle = fopen($this->getPath() . $this->getFileName($v), "r");

        $i = 0;
        $values = [];
        $query = 'INSERT INTO ' . $tableTmp . ' (ip_from, ip_to, country_code, country_name, region_name, city_name, latitude, longitude, zip_code, time_zone) VALUES %s';

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {

            $data = $this->prepare($data);

            $string = implode(',', $data);
            $values[$i] = "($string)"; // php7 optimize

            echo json_encode($values[$i]) . PHP_EOL;

            if (isset($values[self::QUERY_COUNT])) { // isset самый быстрый способ проверить
                $db->execute(sprintf($query, implode(',', $values)));

                $i = 0;
                $values = [];
            }
            $i++;
        }
        // добавляем остаток
        $db->execute(sprintf($query, implode(',', $values)));


        fclose($handle);

        $db->execute("ALTER TABLE $table RENAME TO $tableOld");
        $db->execute("ALTER TABLE $tableTmp RENAME TO $table");

        if ($v == self::IP_V4) {
            $db->execute('ALTER SEQUENCE ipv4_id_seq OWNED BY ipv4.id');
        } else {
            $db->execute('ALTER SEQUENCE ipv6_id_seq OWNED BY ipv6.id');
        }
        $db->execute("DROP TABLE $tableOld");
    }

    /**
     * Подготавливаем данные с csv под структуру базы
     *
     * @param $data
     * @return mixed
     */
    private function prepare($data)
    {
        $data[self::FIELD_IP_FROM] = long2ip($data[self::FIELD_IP_FROM]);
        $data[self::FIELD_IP_TO] = long2ip($data[self::FIELD_IP_TO]);

        array_walk($data, function (&$item, $key) {
            if ($key != self::FIELD_LAT and $key != self::FIELD_LONG) {
                if ($key == self::FIELD_COUNTRY_NAME
                    or $key == self::FIELD_REGION_NAME
                    or $key == self::FIELD_CITY_NAME
                ) {
                    $item = pg_escape_string($item);
                }
                $item = "'$item'";
            }
        });
        return $data;
    }
}