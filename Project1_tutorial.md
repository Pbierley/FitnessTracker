Starting server
 - use cmd line to start sql server 
    - sudo service mysql start

 - (Dont do this until your done)use cmd line to stop server
    - sudo systemctl start mysql

 - Check status of sql
    - sudo service mysql status

 - connect to database as root user to test its working
    - mysql -h 127.0.0.1 -P 3306 -u root -p
    - password is 123456

 - Command to create fitness tracker database 
    - mysql -u root -p fitness_tracker < schema.sql

 - Having issues connecting to the database with an sql tool like dbeaver?
    - Change the bind address to 127.0.0.1 to 0.0.0.0 to open it up outside of wsl
    - change your user permission
    - ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '123456';
    - FLUSH PRIVILEGES;
