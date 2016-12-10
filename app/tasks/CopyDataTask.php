<?php
use Phalcon\Cli\Task;

class CopyDataTask extends Task
{
    public function mainAction()
    {
    	$db = $this->getDi()->getShared('db');

		try {
			$db->begin();
			echo 'begin transaction' . PHP_EOL;

			$db->execute('DROP TABLE IF EXISTS ipv4_tmp');
			$db->execute('CREATE TABLE ipv4_tmp (LIKE ipv4 INCLUDING ALL)');

			echo 'create table' . PHP_EOL;

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
			$values = [];
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {

				echo 'get csv' . PHP_EOL;

				$data[0] = long2ip($data[0]);
				$data[1] = long2ip($data[1]);

				array_walk($data, function(&$item, $key) {
					if ($key != 6 and $key != 7) {

						if ($key == 3 or $key == 4 or $key == 5) {
							$item = pg_escape_string($item);
						}
						$item = "'" . $item . "'";
					}
				});

				$string = implode(',', $data);
				$values[$i] = "($string)"; // php7 optimize

				echo json_encode($values[$i]) . PHP_EOL;

				if ($i >= 1000) {
					//вставить в базу

					// @todo bind params
					$query = 'INSERT INTO ipv4_tmp (ip_from, ip_to, country_code, country_name, region_name, city_name, latitude, longitude, zip_code, time_zone) VALUES %s';
					$db->execute(sprintf($query, implode(',', $values)));

					$i = 0;
					$values = [];
				}

				$i++;
			}

			$query = 'INSERT INTO ipv4_tmp (ip_from, ip_to, country_code, country_name, region_name, city_name, latitude, longitude, zip_code, time_zone) VALUES %s';
			$db->execute(sprintf($query, implode(',', $values)));
			// вставить в базу
			fclose($handle);

			$db->execute('ALTER TABLE ipv4 RENAME TO ipv4_old');
			$db->execute('ALTER TABLE ipv4_tmp RENAME TO ipv4');

			$db->execute('ALTER SEQUENCE ipv4_id_seq OWNED BY ipv4.id');

			$db->execute('DROP TABLE ipv4_old');

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