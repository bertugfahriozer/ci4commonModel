<?php

/**
 * CommonModel class for handling database operations in CodeIgniter 4.
 *
 * This model provides various methods to interact with the database, including
 * inserting, updating, deleting, and retrieving records, as well as supporting
 * complex queries with joins, where conditions, and like patterns.
 *
 * @author Bertuğ Fahri ÖZER <bertugfahriozer@gmail.com>
 * @link https://github.com/bertugfahriozer/ci4commonModel
 * @filesource
 * @license MIT License (https://opensource.org/license/mit)
 */

namespace ci4commonmodel\Models;

class CommonModel
{
    public $db;
    public $forge;

    public function __construct(string $group = 'default')
    {
        $this->db = \Config\Database::connect($group);
        $this->forge = \Config\Database::forge($group);
    }

    /**
     * @param string $table // table name
     * @param string $select // Coluns to select
     * @param array $where // Where conditions
     * @param string $order // Sorting criteria
     * @param int $limit // Limit on the number of results
     * @param int $pkCount // Primary key count
     * @param array $like // Like conditions
     * @param array $orWhere // Or conditions
     * @param array $joins // Join operations
     * @param array $options
     * @return array|null|false Returns the result object on success, null if no results, or false on failure.
     *
     * @example
     * // Example parameters:
     * $table= 'users';
     * $select = 'id, name, email, ...';
     * $where = ['status' => 1, ...];
     * $order = 'created_at DESC';
     * $limit = 10;
     * $pkCount = 1;
     * $like = ['name' => 'John', ...];
     * $orWhere = ['role' => 'admin', ...];
     * $joins = [['table' => 'roles', 'cond' => 'users.role_id = roles.id', 'type' => 'left'], ...];
     *
     * // You can use this function in a Controller or Library like this:
     * $result=$this->commonModel->lists($table, $select, $where, $order, $limit, $pkCount, $like, $orWhere, $joins);
     * // Once the results are returned, you can process the data using a foreach loop:
     * foreach ($result as $row) {
     *     echo $row->name . ' - ' . $row->email;
     * }
     *
     * // If no results are returned, make sure to handle null checks:
     * if (!$result) {
     *     echo "No records found.";
     * }
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     * @version 1.1.1 Added LIMIT functionality.
     * @version 1.1.4 Added LIKE functionality.
     * @version 1.1.8 Added OR WHERE functionality.
     * @version 1.1.9 Added $options and JOIN functionality.
     */
    public function lists(string $table, string $select = '*', array $where = [], string $order = 'id ASC', int $limit = 0, int $pkCount = 0, array $like = [], array $orWhere = [], array $joins = [], array $options = ['isReset' => false]): mixed
    {
        $builder = $this->db->table($table);
        $builder->select($select);
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builder->join($join['table'], $join['cond'], $join['type']);
            }
        }
        $builder->where($where);
        if ($orWhere) $builder->orWhere($orWhere);
        if (!empty($like)) {
            if (count($like) === 1) {
                $builder->like(key($like), reset($like));
            } else {
                $builder->groupStart();
                foreach ($like as $field => $value) {
                    $builder->orLike($field, $value);
                }
                $builder->groupEnd();
            }
        }
        $builder->orderBy($order);
        if ($options['isReset'] == true) return $builder->get()->getRow();
        if ($limit >= 0 || $pkCount >= 0) $builder->limit($limit, $pkCount);
        return $builder->get()->getResult();
    }

    /**
     * Inserts a new record into the specified table and returns the insert ID.
     *
     * This method allows you to insert a new record into a database table.
     * After the record is inserted, it returns the ID of the inserted row.
     *
     * @param string $table
     * @param array $data
     * @return int Returns the ID of the newly inserted record. If the table doesn't have an auto-incremented primary key, it may return 0.
     *
     * @example
     * // Example usage:
     * $table = 'users';
     * $data = [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'status' => 1
     * ];
     *
     * // Insert a new user record and get the insert ID:
     * $insertId = $this->commonModel->create($table, $data);
     *
     * if ($insertId) {
     *     echo "New user created with ID: " . $insertId;
     * } else {
     *     echo "Failed to create user.";
     * }
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function create(string $table, array $data = []): int
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $this->db->insertID();
    }

    /**
     * Inserts multiple records into the specified table.
     *
     * This method inserts an array of data into the table in a batch operation. It is useful for bulk inserts where multiple rows need to be added simultaneously.
     *
     * @param string $table The name of the table where the records will be inserted.
     * @param array $data A multi-dimensional array where each sub-array represents a row of data to insert. Each sub-array's keys should match the column names. Example: [['name' => 'John', 'email' => 'john@example.com'], ['name' => 'Jane', 'email' => 'jane@example.com']].
     *
     * @return mixed Returns true on success, false on failure, or the number of rows inserted (based on the database driver used).
     *
     * @example
     * // Example usage:
     * $table = 'users';
     * $data = [
     *     ['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 1],
     *     ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'status' => 1]
     * ];
     *
     * // Insert multiple records into the users table:
     * $result = $this->commonModel->createMany($table, $data);
     *
     * if ($result) {
     *     echo "Records inserted successfully.";
     * } else {
     *     echo "Failed to insert records.";
     * }
     *
     * @throws InvalidArgumentException If the $data array is empty or invalid.
     * @since 1.1.0
     */
    public function createMany(string $table, array $data): mixed
    {
        $builder = $this->db->table($table);
        return $builder->insertBatch($data);
    }

    /**
     * Updates records in the specified table based on the given conditions.
     *
     * This method updates one or more rows in the table where the specified conditions match.
     * The `$data` array contains the new values for the columns, and the `$where` array specifies the conditions to find the records to update.
     *
     * @param string $table The name of the table where the records will be updated.
     * @param array $data An associative array of column-value pairs that represent the new values. Example: ['name' => 'John', 'email' => 'john@example.com'].
     * @param array $where An associative array of conditions used to filter the records to be updated. Example: ['id' => 1].
     *
     * @return bool Returns true if the update was successful, false otherwise.
     *
     * @example
     * // Example usage:
     * $table = 'users';
     * $data = ['name' => 'John Doe', 'email' => 'john@example.com'];
     * $where = ['id' => 1];
     *
     * // Update the user record with ID 1:
     * $result = $this->commonModel->edit($table, $data, $where);
     *
     * if ($result) {
     *     echo "Record updated successfully.";
     * } else {
     *     echo "Failed to update record.";
     * }
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function edit(string $table, array $data = [], array $where = []): bool
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->update($data);
    }

    /**
     * Deletes records from the specified table based on the given conditions.
     *
     * This method deletes one or more rows in the table where the specified conditions match.
     * The `$where` array is used to specify the conditions for finding the records to be deleted.
     *
     * @param string $table The name of the table from which the records will be deleted.
     * @param array $where An associative array of conditions used to filter the records to be deleted. Example: ['id' => 1].
     *
     * @return bool Returns true if the delete operation was successful, false otherwise.
     *
     * @example
     * // Example usage:
     * $table = 'users';
     * $where = ['id' => 1];
     *
     * // Delete the user record with ID 1:
     * $result = $this->commonModel->remove($table, $where);
     *
     * if ($result) {
     *     echo "Record deleted successfully.";
     * } else {
     *     echo "Failed to delete record.";
     * }
     *
     * @throws InvalidArgumentException If the $where array is empty or invalid.
     * @since 1.0.0
     */
    public function remove(string $table, array $where = []): bool
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->delete();
    }

    /**
     * Selects a single record from the database based on conditions.
     * @param string $table
     * @param array $where
     * @param string $select
     * @param string $order
     * @return object|null Returns the row object on success or null if no result is found.
     * @version 1.1.2 Added ORDER functionality.
     *
     * @example
     * // Example parameters:
     * $table = 'users';
     * $where = ['id' => 1];
     * $select = 'id, name, email';
     * $order = 'created_at DESC';
     *
     * // You can use this function like this:
     * $result = $this->commonModel->selectOne($table, $where, $select, $order);
     *
     * // To display the result:
     * if ($result) {
     *     echo $result->name . ' - ' . $result->email;
     * } else {
     *     echo "No records found.";
     * }
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function selectOne(string $table, array $where = [], string $select = '*', string $order = 'id ASC'): mixed
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->orderBy($order)->get()->getRow();
    }

    /**
     * Checks if there are records in the table where a specified column's value matches any of the provided values.
     *
     * @param string $att
     * @param string $table
     * @param array $where
     * @return int Returns the number of rows that match the condition
     *
     * @example
     * // Example parameters:
     * $att = 'status';
     * $table = 'orders';
     * $where = [1, 2, 3];
     *
     * // You can use this function like this:
     * $count = $this->commonModel->whereInCheckData($att, $table, $where);
     *
     * // To display the count of matching records:
     * echo "Number of matching records: " . $count;
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function whereInCheckData(string $att, string $table, array $where = []): int
    {
        $builder = $this->db->table($table);
        return $builder->whereIn($att, $where, 1)->get()->getNumRows();
    }

    /**
     * Checks if there are any records in the specified table that match the given conditions.
     *
     * @param string $table
     * @param array $where
     * @return int Returns the number of rows that match the condition.
     *
     * @example
     * // Example parameters:
     * $table = 'products';
     * $where = ['category_id' => 10, 'status' => 'active'];
     *
     * // You can use this function like this:
     * $count = $this->commonModel->isHave($table, $where);
     *
     * // To display the count of matching records:
     * echo "Number of matching records: " . $count;
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function isHave(string $table, array $where): int
    {
        $builder = $this->db->table($table);
        return $builder->getWhere($where, 1)->getNumRows();
    }

    /**
     * Counts the number of records in the specified table that match the given conditions.
     *
     * @param string $table
     * @param array $where
     * @return int Returns the count of rows that match the condition
     *
     * @example
     * // Example parameters:
     * $table = 'orders';
     * $where = ['status' => 'completed'];
     *
     * // You can use this function like this:
     * $count = $this->commonModel->count($table, $where);
     *
     * // To display the count of matching records:
     * echo "Number of matching records: " . $count;
     *
     * // If no conditions are specified, the count will include all records in the table:
     * $countAll = $this->commonModel->count($table);
     * echo "Total number of records: " . $countAll;
     *
     * @throws InvalidArgumentException
     * @since 1.0.0
     */
    public function count(string $table, array $where = []): int
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->countAllResults();
    }

    /**
     * Retrieves records from the specified table that match the given conditions and like patterns.
     *
     * @param string $table The name of the table to query.
     * @param array $like Associative array of LIKE conditions, where the keys are column names and the values are the search patterns.
     * @param string $select Columns to select, separated by commas. Defaults to '*' to select all columns.
     * @param array $where Associative array of WHERE conditions.
     *
     * @return object Returns an object containing the result set.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.0.0
     *
     * @example
     * // Example parameters:
     * $table = 'products';
     * $like = ['name' => 'Gadget']; // Search for 'Gadget' in the 'name' column
     * $select = 'id, name, price'; // Select specific columns
     * $where = ['status' => 'available']; // Filter by 'status'
     *
     * // Usage example:
     * $results = $this->commonModel->research($table, $like, $select, $where);
     *
     * // Processing the results:
     * foreach ($results as $row) {
     *     echo $row->id . ' - ' . $row->name . ' - $' . $row->price . '<br>';
     * }
     *
     * // If no records match the criteria, an empty result object is returned.
     */
    public function research(string $table, array $like = [], string $select = '*', array $where = []): object
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->like($like)->get()->getResult();
    }

    /**
     * Retrieves records from the specified table, excluding those where the given key matches any value in the specified array, with optional joins and sorting.
     *
     * @param string $table The name of the table to query.
     * @param string $select Columns to select, separated by commas. Defaults to '*' to select all columns.
     * @param array $joins Array of join conditions, where each element is an associative array with keys 'table', 'cond', and 'type' for the join table, condition, and type respectively.
     * @param string $whereInKey The column to check for exclusion based on the values in $whereInData.
     * @param array $whereInData An array of values that should be excluded from the results.
     * @param string $orderBy Column and direction by which to order the results, defaults to 'queue ASC'.
     *
     * @return object Returns an object containing the result set.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.1.8
     *
     * @example
     * // Example parameters:
     * $table = 'orders';
     * $select = 'id, customer_name, order_date';
     * $joins = [
     *     ['table' => 'customers', 'cond' => 'orders.customer_id = customers.id', 'type' => 'inner']
     * ];
     * $whereInKey = 'order_status';
     * $whereInData = ['canceled', 'returned'];
     * $orderBy = 'order_date DESC';
     *
     * // Usage example:
     * $results = $this->commonModel->notWhereInList($table, $select, $joins, $whereInKey, $whereInData, $orderBy);
     *
     * // Processing the results:
     * foreach ($results as $row) {
     *     echo $row->id . ' - ' . $row->customer_name . ' - ' . $row->order_date . '<br>';
     * }
     *
     * // This will retrieve orders that are not 'canceled' or 'returned', sorted by order date in descending order.
     * // If no records match the criteria, an empty result object is returned.
     */
    public function notWhereInList(string $table, string $select = '*', array $joins = [], string $whereInKey = '', array $whereInData = [], string $orderBy = 'queue ASC'): object
    {
        $builder = $this->db->table($table)->select($select);
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builder->join($join['table'], $join['cond'], $join['type']);
            }
        }
        $builder->whereNotIn($whereInKey, $whereInData)->orderBy($orderBy);
        return $builder->get()->getResult();
    }

    /**
     * Retrieves the list of tables in the database.
     *
     * @return array Returns an array of table names.
     *
     * @since 1.2.0
     * @example
     * // Example usage:
     * $tables = $this->commonModel->getTableList();
     *
     * foreach ($tables as $table) {
     *     echo $table . '<br>';
     * }
     *
     * // This will print the names of all tables in the database.
     */
    public function getTableList()
    {
        return $this->db->listTables();
    }

    /**
     * Creates a new table in the database with the specified fields and options.
     *
     * @param string $table The name of the table to create.
     * @param array $fields An associative array of fields and their data types.
     * @param array $addKeys Optional keys to add to the table.
     * @param array $foreignKeyFilled Optional foreign key constraints.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function newTable(string $table, array $fields, array $addKeys = [
        'keys' = [],
        'primary' => false,
        'unique' => false,
        'keyName' => ''
    ], $foreignKeyFilled = [
        'field'          => '',
        'referenceTable' => '',
        'referenceField' => '',
        'onDelete'       => '',
        'onUpdate'       => '',
        'fkName'         => '',
    ]): bool
    {
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        if (!empty($addKeys['keys'])) {
            $this->forge->addKey($addKeys['keys'], $addKeys['primary'], $addKeys['unique'], $addKeys['keyName']);
        }
        if (!empty($foreignKeyFilled['field'])) {
            $this->forge->addForeignKey($foreignKeyFilled['field'], $foreignKeyFilled['referenceTable'], $foreignKeyFilled['referenceField'], $foreignKeyFilled['onDelete'], $foreignKeyFilled['onUpdate'], $foreignKeyFilled['fkName']);
        }
        $this->forge->addKey($addKeys['keys'], $addKeys['primary'], $addKeys['unique'], $addKeys['keyName']);
        return $this->forge->createTable($table, true);
    }

    /**
     * Removes the specified table from the database.
     *
     * @param string $table The name of the table to remove.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the table name is invalid or not provided.
     * @since 1.2.0
     *
     * @example
     * // Example usage:
     * $table = 'old_table';
     *
     * if ($this->commonModel->removeTable($table)) {
     *     echo 'Table removed successfully.';
     * } else {
     *     echo 'Failed to remove the table.';
     * }
     *
     * // This will delete the 'old_table' from the database.
     */
    public function removeTable($table): bool
    {
        return $this->forge->dropTable($table, true);
    }

    /**
     * Adds a new column to the specified table.
     *
     * @param string $table The name of the table to modify.
     * @param array $fields An associative array of fields and their data types.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function addColumnToTable(string $table, array $fields): bool
    {
        return $this->forge->addColumn($table, $fields);
    }

    /**
     * Removes the specified columns from the table.
     *
     * @param string $table The name of the table to modify.
     * @param array $fields An array of column names to remove.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function removeColumnFromTable(string $table, array $fields)
    {
        return $this->forge->dropColumn($table, $fields);
    }

    /**
     * Updates the name of the specified table.
     *
     * @param string $oldName The current name of the table.
     * @param string $newName The new name for the table.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function updateTableName(string $oldName, string $newName): bool
    {
        return $this->forge->renameTable($oldName, $newName);
    }

    /**
     * Modifies the column information of the specified table.
     *
     * @param string $table The name of the table to modify.
     * @param array $fields An associative array of fields and their new data types.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function modifyColumnInfos(string $table, array $fields): mixed
    {
        return $this->forge->modifyColumn($table, $fields);
    }

    /**
     * Truncates the specified table, removing all records while keeping the table structure intact.
     *
     * @param string $table The name of the table to truncate.
     *
     * @return boolean Returns true on success, false on failure.
     * @version 1.2.0 Changed the method name from `truncateTable` to `emptyTableDatas`.
     * @throws InvalidArgumentException If the table name is invalid or not provided.
     * @since 1.1.9
     *
     * @example
     * // Example usage:
     * $table = 'users';
     *
     * if ($this->commonModel->emptyTableDatas($table)) {
     *     echo 'Table truncated successfully.';
     * } else {
     *     echo 'Failed to truncate the table.';
     * }
     *
     * // This will remove all rows from the 'users' table without deleting the table itself.
     */
    public function emptyTableDatas(string $table): bool
    {
        return $this->db->table($table)->truncate();
    }

    /**
     * Retrieves the fields of the specified table.
     *
     * @param string $tableName The name of the table to retrieve fields from.
     *
     * @return array Returns an array of field objects.
     *
     * @throws InvalidArgumentException If the table name is invalid or not provided.
     * @since 1.2.0
     *
     * @example
     * // Example usage:
     * $tableName = 'users';
     *
     * $fields = $this->commonModel->getTableFields($tableName);
     *
     * foreach ($fields as $field) {
     *     echo $field->name . ' - ' . $field->type . '<br>';
     * }
     *
     * // This will print the names and types of all fields in the 'users' table.
     */
    public function getTableFields($tableName)
    {
        return $this->db->getFieldData($tableName);
    }

    /**
     * Creates a new database with the specified name.
     *
     * @param string $dbName
     *
     * @return boolean
     *
     * @since 1.2.0
     */
    public function newDatabase(string $dbName): bool
    {
        return $this->forge->createDatabase($dbName);
    }

    /**
     * Removes the specified database.
     *
     * @param string $dbName The name of the database to remove.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the database name is invalid or not provided.
     * @since 1.2.0
     *
     * @example
     * // Example usage:
     * $dbName = 'old_database';
     *
     * if ($this->commonModel->removeDatabase($dbName)) {
     *     echo 'Database removed successfully.';
     * } else {
     *     echo 'Failed to remove the database.';
     * }
     *
     * // This will delete the 'old_database' from the server.
     */
    public function removeDatabase(string $dbName): bool
    {
        return $this->forge->dropDatabase($dbName);
    }

    /**
     * Drop a primary key to the specified table.
     *
     * @param string $tableName The name of the table to modify.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function drpPrimaryKey(string $tableName)
    {
        return $this->forge->dropPrimaryKey($tableName);
    }

    /**
     * Drop a key to the specified table.
     *
     * @param string $tableName The name of the table to modify.
     * @param string $keyName The name of the key to drop.
     * @param bool $prefixKeyName Whether to prefix the key name with the table name.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function drpKey(string $tableName, string $keyName, bool $prefixKeyName = true)
    {
        return $this->forge->dropKey($tableName, $keyName, $prefixKeyName);
    }

    /**
     * Drop a foreign key to the specified table.
     *
     * @param string $tableName The name of the table to modify.
     * @param string $foreignKeyName The name of the foreign key to drop.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws InvalidArgumentException If the parameters are invalid.
     * @since 1.2.0
     */
    public function drpForeignKey(string $tableName,string $foreignKeyName)
    {
        return $this->forge->dropForeignKey($tableName, $foreignKeyName);
    }
}
