# CommonModel Library for CodeIgniter 4

`CommonModel` is a versatile and reusable model for CodeIgniter 4, designed to simplify common database operations such as selecting, inserting, updating, and deleting records. This library provides methods that support common SQL features like JOINs, WHERE conditions, LIKE filters, ordering, and more.

## Features

- **Select records** with flexible conditions (`WHERE`, `OR WHERE`, `LIKE`)
- **Insert single or multiple records** into the database
- **Update and delete** records based on conditions
- **Join tables** for more complex queries
- Supports **ordering** and **pagination**
- Easy **counting** and **existence checks** for records
- Built-in support for **like** queries and **batch operations**
- Table and column management (add, remove, modify)
- Database creation and deletion

## Table of Contents

- [CommonModel Library for CodeIgniter 4](#commonmodel-library-for-codeigniter-4)
  - [Features](#features)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
  - [Usage](#usage)
    - [1. Retrieving Records (`lists`)](#1-retrieving-records-lists)
    - [2. Inserting Records (`create`)](#2-inserting-records-create)
    - [3. Batch Insert (`createMany`)](#3-batch-insert-createmany)
    - [4. Updating Records (`edit`)](#4-updating-records-edit)
    - [5. Deleting Records (`remove`)](#5-deleting-records-remove)
    - [6. Count Records (`count`)](#6-count-records-count)
    - [7. Check Record Existence (`isHave`)](#7-check-record-existence-ishave)
    - [8. Complex Queries (`research`)](#8-complex-queries-research)
    - [9. Table and Database Management](#9-table-and-database-management)
      - [Get Table List](#get-table-list)
      - [Create a New Table](#create-a-new-table)
      - [Remove a Table](#remove-a-table)
      - [Add Column to Table](#add-column-to-table)
      - [Remove Column from Table](#remove-column-from-table)
      - [Rename Table](#rename-table)
      - [Modify Column Info](#modify-column-info)
      - [Truncate Table](#truncate-table)
      - [Get Table Fields](#get-table-fields)
      - [Create a New Database](#create-a-new-database)
      - [Remove a Database](#remove-a-database)
      - [Drop Primary Key](#drop-primary-key)
      - [Drop Key](#drop-key)
      - [Drop Foreign Key](#drop-foreign-key)
  - [License](#license)
  - [Author](#author)

## Installation

To use `CommonModel` in your CodeIgniter 4 project, follow these steps:

1. Install with Composer:
    ```bash
    composer require bertugfahriozer/ci4commonmodel
    ```

2. Load the model in your controller:
    ```php
    use ci4commonmodel\Models\CommonModel;

    class ExampleController extends BaseController
    {
        protected $commonModel;

        public function __construct()
        {
            $this->commonModel = new CommonModel();
        }
    }
    ```

## Usage

### 1. Retrieving Records (`lists`)
Fetch records from a database table with flexible filters such as `WHERE`, `OR WHERE`, `LIKE`, `JOIN`, and ordering. Supports limit and pagination.

```php
// Simple usage
$users = $this->commonModel->lists('users', '*', ['status' => 1], 'id DESC', 10);

// Advanced usage with JOIN and LIKE
$joins = [
    ['table' => 'roles', 'cond' => 'users.role_id = roles.id', 'type' => 'left']
];
$like = ['name' => 'John'];
$users = $this->commonModel->lists('users', 'users.*, roles.name as role', ['status' => 1], 'users.id DESC', 10, 0, $like, [], $joins);
```

### 2. Inserting Records (`create`)
Insert a single record into the database and return the newly inserted ID.

```php
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'status' => 1
];

$insertId = $this->commonModel->create('users', $data);
```

### 3. Batch Insert (`createMany`)
Insert multiple records at once.

```php
$data = [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com']
];

$this->commonModel->createMany('users', $data);
```

### 4. Updating Records (`edit`)
Update existing records by specifying the WHERE conditions and the new data.

```php
$data = ['status' => 2];
$where = ['id' => 1];

$this->commonModel->edit('users', $data, $where);
```

### 5. Deleting Records (`remove`)
Delete records from a table based on WHERE conditions.

```php
$where = ['id' => 1];
$this->commonModel->remove('users', $where);
```

### 6. Count Records (`count`)
Count the number of records that match a given condition.

```php
$where = ['status' => 1];
$count = $this->commonModel->count('users', $where);
```

### 7. Check Record Existence (`isHave`)
Check whether a record exists in a table with a specified condition.

```php
$where = ['id' => 1];
$isExist = $this->commonModel->isHave('users', $where);
```

### 8. Complex Queries (`research`)
Search records using LIKE queries and filtering by conditions.

```php
$like = ['name' => 'John'];
$where = ['status' => 1];

$results = $this->commonModel->research('users', $like, '*', $where);
```

### 9. Table and Database Management

#### Get Table List
```php
$tables = $this->commonModel->getTableList();
```

#### Create a New Table
```php
$fields = [
    'id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
        'auto_increment' => true,
    ],
    'title' => [
        'type' => 'VARCHAR',
        'constraint' => '100',
        'unique' => true,
    ]
];
$this->commonModel->newTable('blog', $fields);
```

#### Remove a Table
```php
$this->commonModel->removeTable('blog');
```

#### Add Column to Table
```php
$fields = [
    'preferences' => ['type' => 'TEXT', 'after' => 'email'],
];
$this->commonModel->addColumnToTable('users', $fields);
```

#### Remove Column from Table
```php
$this->commonModel->removeColumnFromTable('users', ['preferences']);
```

#### Rename Table
```php
$this->commonModel->updateTableName('old_table', 'new_table');
```

#### Modify Column Info
```php
$fields = [
    'old_name' => [
        'name' => 'new_name',
        'type' => 'TEXT',
        'null' => false,
    ],
];
$this->commonModel->modifyColumnInfos('users', $fields);
```

#### Truncate Table
```php
$this->commonModel->emptyTableDatas('users');
```

#### Get Table Fields
```php
$fields = $this->commonModel->getTableFields('users');
```

#### Create a New Database
```php
$this->commonModel->newDatabase('new_db');
```

#### Remove a Database
```php
$this->commonModel->removeDatabase('old_db');
```

#### Drop Primary Key
```php
$this->commonModel->drpPrimaryKey('users');
```

#### Drop Key
```php
$this->commonModel->drpKey('users', 'my_key_name');
```

#### Drop Foreign Key
```php
$this->commonModel->drpForeignKey('orders', 'fk_orders_users');
```

## License

This project is licensed under the MIT License. See the [LICENSE](https://opensource.org/license/mit) file for more details.

## Author

[Bertuğ Fahri ÖZER](https://github.com/bertugfahriozer)
