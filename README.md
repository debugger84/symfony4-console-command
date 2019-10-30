A template for console command
===

This project uses laradoc to orchestrate environment of a project. The Symfony framework have been chosen to organize code architecture of the code. To work with DB the Specification pattern in conjunction with the Repository pattern were used. To decrease coupling of the system the event approach have been used.   

How to start 
===
* Pull the project to your local directory.
* Run `make start`. All containers will start.
* If you want to test, than run `make test`
* Add data to database and run `make run-command`

If you want to know all available commands from the Makefile - run `make help`  
