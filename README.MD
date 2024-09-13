# [CommonModel Library for CodeIgniter 4](#commonmodel-library-for-codeigniter-4)

`CommonModel` is a versatile and reusable model for CodeIgniter 4, designed to simplify common database operations such as selecting, inserting, updating, and deleting records. This library provides methods that support common SQL features like JOINs, WHERE conditions, LIKE filters, and ordering.

## [Features](#features)

- **Select records** with flexible conditions (`WHERE`, `OR WHERE`, `LIKE`)
- **Insert single or multiple records** into the database
- **Update and delete** records based on conditions
- **Join tables** for more complex queries
- Supports **ordering** and **pagination**
- Easy **counting** and **existence checks** for records
- Built-in support for **like** queries and **batch operations**

- [CommonModel Library for CodeIgniter 4](#commonmodel-library-for-codeigniter-4)
- [Features](#features)
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
- [License](#license)
- [Author](#author)

## [Installation](#installation)

To use `CommonModel` in your CodeIgniter 4 project, follow these steps:

1. Install with Composer into your project:
    ```bash
    composer require bertugfahriozer/ci4commonmodel
    ```

2. Load the model in your controller:
    ```PHP
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

## [Usage](#usage)
### [1. Retrieving Records (`lists`)](#1-retrieving-records-lists)
The `lists` method allows you to fetch records from a database table with flexible filters such as `WHERE`, `OR WHERE`, `LIKE`, `JOIN`, and ordering. Optionally, limit and pagination can be applied.

```PHP
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'status' => 1
];

$insertId = $this->commonModel->create('users', $data);
```

### [2. Inserting Records (`create`)](#2-inserting-records-create)
Insert a single record into the database and return the newly inserted ID.
```PHP
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'status' => 1
];

$insertId = $this->commonModel->create('users', $data);
```

### [3. Batch Insert (`createMany`)](#3-batch-insert-createmany)
Insert multiple records at once with the createMany method.

```PHP
$data = [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com']
];

$this->commonModel->createMany('users', $data);
```

### [4. Updating Records (`edit`)](#4-updating-records-edit)
Update existing records by specifying the WHERE conditions and the new data.

```PHP
$data = ['status' => 2];
$where = ['id' => 1];

$this->commonModel->edit('users', $data, $where);
```

### [5. Deleting Records (`remove`)](#5-deleting-records-remove)
Delete records from a table based on WHERE conditions.

```PHP
$where = ['id' => 1];
$this->commonModel->remove('users', $where);
```

### [6. Count Records (`count`)](#6-count-records-count)
Count the number of records that match a given condition.

```PHP
$where = ['status' => 1];
$count = $this->commonModel->count('users', $where);
```

### [7. Check Record Existence (`isHave`)](#7-check-record-existence-ishave)
Check whether a record exists in a table with a specified condition.

```PHP
$where = ['id' => 1];
$isExist = $this->commonModel->isHave('users', $where);
```

### [8. Complex Queries (`research`)](#8-complex-queries-research)
Use research for searching records using LIKE queries and filtering by conditions.

```PHP
$like = ['name' => 'John'];
$where = ['status' => 1];

$results = $this->commonModel->research('users', $like, '*', $where);
```

### [License](#license)
This project is licensed under the MIT License. See the [LICENSE](https://opensource.org/license/mit) file for more details.

# [Author](#author)
[Bertuğ Fahri ÖZER](https://github.com/bertugfahriozer)