<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class MY_Model
 *
 * @class MY_Model
 * @extends CI_Model
 *
 * @abstract
 */
class MY_Model extends CI_Model
{

    /**
     * Table name. Need redefine in inheritable class
     *
     * @const string TABLE_NAME
     */
    const TABLE_NAME = null;


    /**
     * @var int $id
     */
    public $id;


    /**
     * MY_Model constructor.
     *
     * @param array $_data_
     */
    public function __construct(array $_data_ = [])
    {
        parent::__construct();

        if (isset($_data_['id'])) {
            throw new InvalidArgumentException('Field `id` can not be set');
        }

        foreach ($_data_ as $key => $value) {
            if (property_exists(get_called_class(), $key)) {
                $this->{$key} = $value;
            } else {
                throw new InvalidArgumentException("Property {$key} does not exists");
            }
        }
    }


    /**
     * Get DB instance of CodeIgniter
     *
     * @return CI_DB_query_builder|CI_DB_mysqli_driver
     */
    static protected function &db()
    {
        return get_instance()->db;
    }


    /**
     * Get all faqs
     *
     * @param array $_where_ : ex. ['name' => $name, 'title !=' => $title, 'status >' => $status]
     * @param string $_order_ : ex. '<field name> ASC|DESC, etc'
     * @param int $_limit_
     * @param int $_offset_
     *
     * @return static
     */
    static public function get($_where_ = null, $_order_ = null, $_limit_ = null, $_offset_ = null)
    {
        // Get CI_DB instance
        $db = &self::db();

        // If set 'where' parameter
        if (!empty($_where_)) {
            $db->where($_where_);
        }

        // If set 'order' parameter
        if (!empty($_order_)) {
            $db->order_by($_order_);
        }

        return $db->get(static::TABLE_NAME, $_limit_, $_offset_)->result(get_called_class());
    }


    /**
     * Get by id
     *
     * @param int $_id_
     *
     * @return static
     */
    static public function get_by_id($_id_)
    {
        return
            self::db()->where('id', $_id_)->get(static::TABLE_NAME)->row(0, get_called_class());
    }


    /**
     * Delete row by id
     *
     * @param int $_id_
     *
     * @return mixed
     */
    static public function delete_by_id($_id_)
    {
        return
            self::db()->where('id', $_id_)->delete(static::TABLE_NAME);
    }


    /**
     * Delete rows in table by conditions
     *
     * @param array $_where_ : ex. ['name' => $name, 'title !=' => $title, 'status >' => $status]
     *
     * @return mixed
     */
    static public function delete_where($_where_)
    {
        return
            self::db()->where($_where_)->delete(static::TABLE_NAME);
    }


    /**
     * Create row data (insert)
     *
     * @param array $_data_ :
     *          ex. ['name' => 'Test', 'image' => 'cat.jpg']
     *
     * @return false|static
     */
    static public function create($_data_)
    {
        // Get Codeigniter DB instance
        $DB =& self::db();

        // Insert data
        $rs = $DB->insert(static::TABLE_NAME, $_data_);

        // If insert has been successful
        if ($rs) {
            $rs = new static($_data_);

            // Get id of inserted data
            $rs->id = $DB->insert_id();
        }

        return $rs;
    }


    /**
     * Update rows in table
     *
     * @param array $_where_ : where conditions
     *          ex. ['color' => '#FFF', 'background' => 'black']
     * @param array $_data_ : data to update ['<field name>' => '<new value>']
     *          ex. ['background' => 'blue']
     *
     * @return bool
     */
    static public function update_where($_where_, $_data_)
    {
        // Get CI_DB instance
        $db = self::db();

        // Set where
        $db->where($_where_);

        // Set
        $db->set($_data_);

        // Execute update rows
        return $db->update(static::TABLE_NAME);
    }


    /**
     * Update row data in table by id
     *
     * @param int $_id_ : id of desired row
     * @param array $_data_ : data to update ['<field name>' => '<new value>']
     *          ex. ['background' => 'blue']
     *
     * @return bool
     */
    static public function update_by_id($_id_, $_data_)
    {
        // Get CI_DB instance
        $db = self::db();

        // Where `id` is _id_
        $db->where('id', $_id_);

        // Set
        $db->set($_data_);

        // Execute update rows
        return $db->update(static::TABLE_NAME);
    }


    /**
     * Save data or if not exist create
     *
     * @return bool
     */
    public function save()
    {
        // Set data
        $this->db->set(get_object_vars($this)); // get only public properties

        if (empty($this->id)) {

            // Execute update rows
            $rs = $this->db->insert(static::TABLE_NAME);

            // Get id of inserted data
            $this->id = $this->db->insert_id();

        } else {

            // Where `id` is _id_
            $this->db->where('id', $this->id);

            // Execute update rows
            $rs = $this->db->update(static::TABLE_NAME);

        }

        return $rs;
    }

}
