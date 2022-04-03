# Logic
Please find a logic test script as `foobar.php`.

# User upload

### Requirements
- PHP >= 7.4
- PDO MySQL extension
- composer (2.3.3)

### CSV format
A comma sign is used as a separator.
The first line of the CSV file represents a header line (`name,surname,email`).

Columns description (for more details see the Configuration section)

| Column    | DB                                        | Transformer    | Validator                            |
|-----------|-------------------------------------------|----------------|--------------------------------------|
| name      | varchar(255), nullable=false              | lower, ucfirst | string: min_length=1, max_length=255 |
| surname   | varchar(255), nullable=false              | lower, ucfirst | string: min_length=1, max_length=255 |
| email     | varchar(255), nullable=false, unique=true | lower          | email                                | 

### Distribution
- As a standalone phar file (see the Build section)
- As a folder (dependencies must be installed, see the Build section)

### Usage
```console
% php user_upload.php --help                                                               
usage: user_upload.php  [-u=username] [-p=password] [-h=hostname|hostname:port] [-d=dbname] [--help] [--file=CSV filename] [--create_table] [--dry_run] [--log]
[-u=username] MySQL username
[-p=password] MySQL password
[-d=dbname] MySQL database
[-h=hostname|hostname:port] MySQL hostname
[--file=CSV filename] This is the name of the CSV file to be parsed. The following options are required: [-u=user] [-p=password] [-d=database] [-h=hostname|hostname:port]
[--create_table] Will cause the MySQL users table to be built. The following options are required: [-u=user] [-p=password] [-d=database] [-h=hostname|hostname:port]
[--dry_run] Should be used with --file directive, omits all database interaction
[--log] Write log
```

#### Output

In case of any failure the first block will be an error block.
Each error has a reference to a line in the CSV file.

#### Error output

```console
Duplicate entry 'ham@seek.com' for key 'users.email' at line 8
Email is not valid email='edward@jikes@com.au' at line 12
```

#### Stat block 

```console
* Processed:11
* Inserted: 9
* Skipped:  2
```

- `Processed` - a total number of processed lines from the CSV file.
- `Inserted` - a number of inserted rows into DB.
- `Skipped` - a number of skipped rows. A row can be skipped in case of whether invalid value or DB error.

### Log
In case when we want to keep history of the script run we can specify the `--log` option.
Then the log file will be created at the same directory with the script/phar file (file name template: `uploader-[year]-[month]-[day].log`).

### Configuration
A main configuration is located at the `config.php` file.

- `importTableName`  A database table name that holds uploaded data (default: `users`)
- `csvSeparator` A separator sign that is used at CSV file (default: `,`)
- `columnMapping` Holds columns metadata. A key stands for a column name. A value stands for an array of parameters.
To add a new column just describe metadata in this section.

Each column can be described by means of the following parameters:
- `type` [string|integer] A value data type.
- `nullable` [bool] Allows null value.
- `unique` [bool] Unique constraint.
- `validator` [array] An array of validators that will be applied to the column value before inserting. See description below.
- `transformer` [array] An array of transformers that will be applied to the column value. See description below.


#### Supported validators:

| Validator | Options                | Description                          |
|-----------|------------------------|--------------------------------------|
| email     | none                   | Checks that a value is a valid email |
| string    | min_length, max_length | Checks that a value is a string      | 


#### Supported transformers:

| Transformer | Description                                |
|-------------|--------------------------------------------|
| lower       | Makes a string lowercase                   |
| ucfirst     | Makes a string's first character uppercase |


#### Default column mapping
```php
[
    'name' => [
        'type' => 'string',
        'nullable' => false,
        'validator' => ['string' => ['min_length' => 1, 'max_length' => 255]],
        'transformer' => ['lower', 'ucfirst']
    ],
    'surname' => [
        'type' => 'string',
        'nullable' => false,
        'validator' => ['string' => ['min_length' => 1, 'max_length' => 255]],
        'transformer' => ['lower', 'ucfirst']
    ],
    'email' => [
        'type' => 'string',
        'unique' => true,
        'nullable' => false,
        'validator' => ['email'],
        'transformer' => ['lower']
    ]
]
```

### Build
A composer tool is required (Tested with the `2.3.3` version).

Install dependencies:
- `composer install`

#### PHAR
To build an executable phar run `composer box`.
A built executable can be found at `./build/user_upload.phar`

### Test
PHPstan + UnitTest
- `composer test`

### Docker
Test MySQL server

- Create MySQL image: `docker build -t testdb .`
- Run MySQL: `docker run --detach --name=testdb --publish 3306:3306 testdb`

* Database: `customer`
* User: `app`
* Password: `password`
* Host: `127.0.0.1`

#### Upload file
```console
php user_upload.php --file ./data/users.csv -u user -p password -d customer -h 127.0.0.1
```

#### Upload file with dry_run
```console
php user_upload.php --file ./data/users.csv -u user -p password -d customer -h 127.0.0.1 --dry_run
```

#### Create table 
```console
php user_upload.php --create_table -u user -p password -h 127.0.0.1 -d customer
```

#### Different MySQL port (for example 6603)
```console
php user_upload.php --create_table -u user -p password -h 127.0.0.1:6603 -d customer
```
