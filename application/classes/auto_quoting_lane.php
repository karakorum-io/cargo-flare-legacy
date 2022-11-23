<?php

class AutoQuotingLane extends FdObject
{
    const TABLE = "app_autoquoting_lanes";
    const CITIES_TABLE = "app_autoquoting_cities";
    const VEHICLES_TABLE = "app_autoquoting_vehicles";

    const PRICE_TYPE_BASE = 'base';
    const PRICE_TYPE_CPM = 'cpm';

    protected $memberObjects = array();

    public function duplicate($seasonId = null)
    {
        if (!ctype_digit((string) $seasonId)) {
            throw new FDException("Invalid Season ID");
        }

        try {
            if (is_null($seasonId)) {
                $this->db->transaction("start");
            }

            $cities = $this->db->selectRows("*", self::CITIES_TABLE, "WHERE `lane_id` = {$this->id}");
            if ($this->db->isError) {
                throw new FDException("MySQL query error");
            }

            $vehicles = $this->db->selectRows("*", self::VEHICLES_TABLE, "WHERE `lane_id` = {$this->id}");
            if ($this->db->isError) {
                throw new FDException("MySQL query error");
            }

            $lane = $this->selfclone();
            if (!is_null($seasonId)) {
                $lane->update(array('season_id' => $seasonId));
            }
            foreach ($cities as $city) {
                unset($city['id']);
                $city['lane_id'] = $lane->id;
                $this->db->insert(self::CITIES_TABLE, $city);
            }
            foreach ($vehicles as $vehicle) {
                unset($vehicle['id']);
                $vehicle['lane_id'] = $lane->id;
                $this->db->insert(self::VEHICLES_TABLE, $vehicle);
            }
            if (is_null($seasonId)) {
                $this->db->transaction("commit");
            }

        } catch (FDException $e) {
            if (is_null($seasonId)) {
                $this->db->transaction("rollback");
            }

            throw $e;
        }
    }

    /**
     * @param bool $reload
     * @return AutoQuotingSeason
     */
    public function getSeason($reload = false)
    {
        if ($reload || !in_array('season', $this->memberObjects)) {
            $season = new AutoQuotingSeason($this->db);
            $season->load($this->season_id);
            $this->memberObjects['season'] = $season;
        }
        return $this->memberObjects['season'];
    }

    public function checkAccess($owner_id)
    {
        $this->getSeason()->checkAccess($owner_id);
    }

    public function getBasePrice()
    {
        return number_format($this->base_price, 2);
    }

    public function getBaseOrCPMPrice()
    {
        if ($this->price_type == self::PRICE_TYPE_BASE) {
            return number_format($this->base_price, 2);
        } elseif ($this->price_type == self::PRICE_TYPE_CPM) {
            return number_format($this->cpm_price, 2);
        } else {
            return 0.00;
        }

    }
    public function getModified($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->modified));
    }

    public function getCitiesArray()
    {
        $sql = "
            SELECT
                lc.`id`,
                lc.`type`,
                lc.`is_active`,
                lc.`city_id`,
                c.`city`,
                c.`state`,
                lc.`surcharge`
            FROM
                " . self::CITIES_TABLE . " lc,
                `cities` c
            WHERE
                lc.`city_id` = c.`id`
                AND lc.`lane_id` = {$this->id}";
        $result = $this->db->query($sql);
        if ($this->db->isError) {
            throw new FDException("MySQL query error");
        }

        $rows = array();
        while ($row = $this->db->fetch_row($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getVehiclesArray()
    {
        $vehicles = array();
        $rows = $this->db->selectRows("`vehicle_type_id`, `surcharge`", self::VEHICLES_TABLE, "WHERE `lane_id` = '{$this->id}'");
        foreach ($rows as $row) {
            $vehicles[$row['vehicle_type_id']] = $row['surcharge'];
        }
        return $vehicles;
    }

    public function updateVehicleSurcharges($data)
    {
        if (!is_array($data)) {
            throw new FDException("Invalid input data");
        }

        if (count($data) > 0) {
            $sql = "INSERT INTO " . self::VEHICLES_TABLE . " (`lane_id`, `vehicle_type_id`, `surcharge`) VALUES ";
            $sql_lines = array();
            foreach ($data as $key => $value) {
                $sql_lines[] = "({$this->id}, " . (int) $key . ", " . number_format((float) $value, 2, ".", "") . ")";
            }
            $sql .= implode(", ", $sql_lines);
            $sql .= " ON DUPLICATE KEY UPDATE `surcharge` = VALUES(`surcharge`)";
            $this->db->query($sql);
            if ($this->db->isError) {
                throw new FDException("MySQL query error");
            }

        }
    }

    public function updateOriginSurcharges($data, $active_data)
    {
        if (!is_array($data)) {
            throw new FDException("Invalid input data");
        }

        if (count($data) > 0) {
            $sql = "INSERT INTO " . self::CITIES_TABLE . " (`lane_id`, `type`, `is_active`, `city_id`, `surcharge`) VALUES ";
            $sql_lines = array();
            foreach ($data as $key => $value) {
                $sql_lines[] = "({$this->id}, 0, " . ((isset($active_data[$key])) ? 1 : 0) . ", " . (int) $key . ", " . number_format((float) $value, 2, ".", "") . ")";
            }
            $sql .= implode(", ", $sql_lines);
            $sql .= " ON DUPLICATE KEY UPDATE
                    `is_active` = VALUES(`is_active`),
                    `surcharge` = VALUES(`surcharge`)
                ";
            $this->db->query($sql);
            if ($this->db->isError) {
                throw new FDException("MySQL query error");
            }

        }
    }

    public function updateDestinationSurcharges($data, $active_data)
    {
        if (!is_array($data)) {
            throw new FDException("Invalid input data");
        }

        if (count($data) > 0) {
            $sql = "INSERT INTO " . self::CITIES_TABLE . " (`lane_id`, `type`, `is_active`, `city_id`, `surcharge`) VALUES ";
            $sql_lines = array();
            foreach ($data as $key => $value) {
                $sql_lines[] = "({$this->id}, 1, " . ((isset($active_data[$key])) ? 1 : 0) . ", " . (int) $key . ", " . number_format((float) $value, 2, ".", "") . ")";
            }
            $sql .= implode(", ", $sql_lines);
            $sql .= " ON DUPLICATE KEY UPDATE
                    `is_active` = VALUES(`is_active`),
                    `surcharge` = VALUES(`surcharge`)
                ";
            $this->db->query($sql);
            if ($this->db->isError) {
                throw new FDException("MySQL query error");
            }

        }
    }
}
