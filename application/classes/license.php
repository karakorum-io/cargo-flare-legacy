<?php

/**
 * @version        1.0
 * @since        21.08.12
 * @author        Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address        11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email        techsupport@intechcenter.com
 * @copyright    2012 Intechcenter. All Rights Reserved
 *
 * @property int $id
 * @property int $owner_id
 * @property int $order_id
 * @property int $users
 * @property string $expire
 * @property string $created
 * @property int $period_type
 * @property int $product_id
 * @property int $renewal_product_id
 * @property int $renewal_users
 */
class License extends FdObject
{

    const TABLE = 'licenses';
    const DEFAULT_STORAGE_NAME = 'Default 500MB storage';
    const DEFAULT_NONE = "None";
    const STORAGE_500 = 524288000; //500MB
    const STORAGE_1024 = 1073741824; //1GB
    const STORAGE_2048 = 2147483648; //2GB

    /**
     * @param $member_id
     *
     * @return bool
     */
    public function loadCurrentLicenseByMemberId($member_id)
    {
        $row = $this->db->selectRow("id", self::TABLE, "WHERE
																		`owner_id` = " . (int)$member_id . "
																		ORDER BY `expire` DESC
																		LIMIT 0, 1
																");
        if (!empty($row)) {
            $this->load($row['id']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Additional users count for license
     *
     */
    public function getAdditionalLicenseUsersByMemberId($member_id)
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (!ctype_digit((string)$member_id))
            throw new FDException(get_class($this) . "->load: invalid owner_id");

        $row = $this->db->selectRow("id", self::TABLE, "WHERE
																		`owner_id` = " . (int)$member_id . "
																		ORDER BY `expire` DESC
																		LIMIT 0, 1
																");
        if (!empty($row)) {
            $this->load($row['id']);
            return $this->users;
        } else {
            return 0;
        }
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        $order = new Orders($this->db);
        $order->load($this->order_id);
        return $order;
    }

    /**
     * Get current license name
     * @return string
     */
    public function getLicenseName()
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->product_id > 0) {
            $product = new Product($this->db);
            $product->load($this->product_id);
            return $product->name;
        } else {
            return "N/A";
        }
    }

    public function getStorageName($price = false)
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->storage_id > 0) {
            $product = new Product($this->db);
            $product->load($this->storage_id);
            if ($price) {
                return $product->name . " ($" . $product->price . ")";
            } else {
                return $product->name;
            }

        } else {
            if ($price) {
                return self::DEFAULT_STORAGE_NAME . " ($0.00)";
            } else {
                return self::DEFAULT_STORAGE_NAME;
            }

        }
    }

    public function getAddonAqName($price = false)
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->addon_aq_id > 0) {
            $product = new Product($this->db);
            $product->load($this->addon_aq_id);
            if ($price) {
                return $product->name . " ($" . $product->price . ")";
            } else {
                return $product->name;
            }

        } else {
            return self::DEFAULT_NONE;
        }
    }

    public function getNextStorageName($price = false)
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->renewal_storage_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_storage_id);
            if ($price) {
                return $product->name . " ($" . $product->price . ")";
            } else {
                return $product->name;
            }

        } else {
            if ($price) {
                return self::DEFAULT_STORAGE_NAME . " ($0.00)";
            } else {
                return self::DEFAULT_STORAGE_NAME;
            }

        }
    }

    public function getNextAddonAqName($price = false)
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->renewal_addon_aq_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_addon_aq_id);
            if ($price) {
                return $product->name . " ($" . $product->price . ")";
            } else {
                return $product->name;
            }

        } else {
            return " None ";
        }
    }


    /**
     * Get next license name
     * @return string
     */
    public function getNextLicenseName()
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->renewal_product_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_product_id);
            return $product->name . " ($" . $product->price . ")";
        } else {
            throw new FDException("Renewal product not found");
        }
    }

    public function getNextLicenseType()
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->renewal_product_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_product_id);
            return Product::$period_name[$product->period_id];
        } else {
            throw new FDException("Renewal product not found");
        }
    }


    public function getLicenseNameForChangeUsers()
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->period_type > 0) {
            $row = $this->db->selectRow("id", Product::TABLE, "WHERE
																		    `period_id` = " . $this->period_type . "
																				AND type_id = '" . Product::TYPE_ADDITIONAL . "'
																				AND is_online = 1
																				AND is_delete <> 1
																		LIMIT 0, 1
																");
            if (!empty($row)) {
                $product = new Product($this->db);
                $p = $product->load($row["id"]);
                return $p;
            } else {
                throw new FDException("Action is unavaible.");
            }
        } else {
            throw new FDException("Action is unavaible.");
        }
    }

    public function getAddonAQForBuy()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->period_type > 0) {
            $row = $this->db->selectRow("id", Product::TABLE, "WHERE
																		    `period_id` = " . $this->period_type . "
																				AND type_id = '" . Product::TYPE_ADDON_AQ . "'
																				AND is_online = 1
																				AND is_delete <> 1
																		LIMIT 0, 1
																");
            if (!empty($row)) {
                $product = new Product($this->db);
                $p = $product->load($row["id"]);
                return $p;
            } else {
                throw new FDException("Action is unavaible.");
            }
        } else {
            throw new FDException("Action is unavaible.");
        }
    }



    /**
     * Get Amount for periodic payments
     * @return decimal
     */
    public function getLicensePeriodicPayment()
    {

        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");

        if ($this->renewal_product_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_product_id);
            $renewal_price = $product->price;
            $renewal_additional = $this->getAdditionalsTotal();

            $renewal_storage = $this->getRenewalStoragesTotal();
            $renewal_addon_aq = $this->getRenewalAddonAqTotal();

            $renewal_total = $renewal_price + $renewal_additional + $renewal_storage + $renewal_addon_aq;

            return $renewal_total;
        } else {
            return 0;
        }
    }

    public function getAdditionalsTotal()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if (is_null($this->renewal_product_id))
            throw new FDException(get_class($this) . "->load: License ID not set");

        if ($this->renewal_users > 0) {

            $product = new Product($this->db);
            $product->load($this->renewal_product_id);

            $row = $this->db->selectRow("id", Product::TABLE, "WHERE
																						`period_id` = " . $product->period_id . "
																						AND type_id = '" . Product::TYPE_ADDITIONAL . "'
																						AND is_online = 1
																						AND is_delete <> 1
																				LIMIT 0, 1
																		");
            if (!empty($row)) {

                $pr = new Product($this->db);
                $pr->load($row["id"]);
                return $this->renewal_users * $pr->price;
            } else {
                throw new FDException("License ID for Additional users not set");
                return 0; //
            }
        } else {
            return 0;
        }
    }

    public function getStoragesTotal()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if ($this->storage_id > 0) {
            $product = new Product($this->db);
            $product->load($this->storage_id);
            return $product->price;
        } else {
            return 0;
        }
    }

    public function getRenewalStoragesTotal()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if ($this->renewal_storage_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_storage_id);
            return $product->price;
        } else {
            return 0;
        }
    }

    public function getAddonAqTotal()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if ($this->addon_aq_id > 0) {
            $product = new Product($this->db);
            $product->load($this->addon_aq_id);
            return $product->price;
        } else {
            return 0;
        }
    }

    public function getRenewalAddonAqTotal()
    {
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if ($this->renewal_addon_aq_id > 0) {
            $product = new Product($this->db);
            $product->load($this->renewal_addon_aq_id);
            return $product->price;
        } else {
            return 0;
        }
    }



    public function getCurrentStorageSpace(){
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        if ($this->storage_id > 0) {
            $product = new Product($this->db);
            $product->load($this->storage_id);
            return $product->space;
        } else {
            return self::STORAGE_500;
        }
    }

    public function getUsedStorageSpace(){
        if (is_null($this->db))
            throw new FDException(get_class($this) . "->load: DB helper not set");
        if (is_null($this->id))
            throw new FDException(get_class($this) . "->load: License ID not set");
        //if ($this->storage_id > 0) {

            $used = $this->db->selectRow("SUM(size) AS used", "app_uploads", "WHERE owner_id='" . $this->owner_id . "'");
            return (float)$used["used"];

        //} else {
        //    return self::STORAGE_500;
        //}
    }



}
