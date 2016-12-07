<?php
use Phalcon\Cli\Task;

class CopyDataTask extends Task
{
    public function mainAction()
    {
    	$db = $this->getDi()->get('db');

		try {
			$db->begin();

			$db->execute('DROP TABLE IF EXISTS ip4_tmp');
			$db->execute('CREATE TABLE ip4_tmp (LIKE ip4 INCLUDING ALL)');

			$handle = fopen(
				BASE_PATH
				. DIRECTORY_SEPARATOR
				. 'public'
				. DIRECTORY_SEPARATOR
				. 'files'
				. DIRECTORY_SEPARATOR
				. "IP2LOCATION-LITE-DB11.CSV",
				"r");

			$i = 0;
			$values = new \SplFixedArray(1000);
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {

				$data[0] = long2ip($data[0]);
				$data[1] = long2ip($data[1]);

				$string = implode(',', $data);
				$values[$i] = "($string)"; // php7 optimize

				if ($i >= 1000) {
					//вставить в базу

					// @todo bind params
					$query = 'INSERT INTO ipv4 ip_from, ip_to, country_code, country_name, region_name, city_name, latitude, longtitude, zip_code, time_zone VALUES %s';
					$db->execute(sprintf($query, implode(',', $values)));

					$values = new \SplFixedArray(1000);
				}
			}

			$query = 'INSERT INTO ipv4 ip_from, ip_to, country_code, country_name, region_name, city_name, latitude, longtitude, zip_code, time_zone VALUES %s';
			$db->execute(sprintf($query, implode(',', $values)));
			// вставить в базу
			fclose($handle);

			// обойти csv и вставить по строчно
			// когда все обойдет, надо сделать ренейм
			// поставить ip4r

			$db->execute('DROP TABLE IF EXISTS ip6_tmp');
			$db->execute('CREATE TABLE ip6_tmp (LIKE ip6 INCLUDING ALL)');

    		$db->commit();
		} catch (Exception $e) {
    		
			print_r($e->getMessage());

    		$db->rollback();
		}
    	

    	// начать транзакцию
    	// создать копию таблиц
    	// заполнить копию таблиц данными
    	// изменить sequence
    	// переименовать таблицы
    	// удалить старую таблицу
        // COPY ip2location_db11_ipv6 FROM 'IP2LOCATION-LITE-DB11.IPV6.CSV' WITH CSV QUOTE AS '"';
        // COPY ip2location_db11_ipv6 FROM 'IP2LOCATION-LITE-DB11.IPV6.CSV' WITH CSV QUOTE AS '"';
    }
}