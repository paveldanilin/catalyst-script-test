## User upload


## Docker

Test MySQL server

- Create MySQL image: `docker build -t testdb .`
- Run MySQL: `docker run --detach --name=testdb --publish 3306:3306 testdb`

* Database: `customer`
* User: `app`
* Password: `password`
