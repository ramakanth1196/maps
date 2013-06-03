We have used 

Xampp server
Download it from here http://www.apachefriends.org/en/xampp-windows.html#641

Python 2.7
Download it from here http://www.python.org/download/releases/2.7/

MysqlDb
Download it from here http://www.lfd.uci.edu/~gohlke/pythonlibs/#mysql-python

File included:(in maps folder)
	new.php, clickupdate.php, db.php, news2map.sql and a js folder

put maps folder in c://xampp/htdocs/

To run hacknews:
	Make a new database using phpmyadmin name it news2map and import file news2map.sql it will create required table.

now run parser.py it will fill all tables

and now your application is good to go.

To run application
go to browser type 
localhost/maps/news.php