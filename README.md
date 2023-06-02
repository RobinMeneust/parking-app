# PARK'O TOP

## About

A web app that searches for nearby parking lots (in France) and stores the user history to show him his expenses and other statistics.

## Dependencies

- Docker
- Docker Compose

Please note that this project doesn't work offline

## Important

You may need to wait a little after setting up the website, before using elements using the database, since it needs to load before.
For instance you may get the warning : `Warning: mysqli_connect(): (HY000/2002)`. In that case, just wait a couple of minutes.

## Installation

### How to install Docker & Docker Compose

#### Linux

Depending on your distribution:
- Red Hat & derivatives: `sudo yum install docker `
- Debian & derivatives: `sudo apt install docker.io`
- From the installation script: `curl −s https://get.docker.com/ | sudo sh`

Then:
- Install Docker Compose: `sudo apt install docker-compose`

#### Windows

- Download & install: https://docs.docker.com/desktop/install/windows-install/
- Run `wsl --update`

### How to build the containers

Run the following command in the root of this project (parking-app/): `docker-compose up -d`


### How to stop the containers

Run the following command in the root of this project (parking-app/): `docker-compose down`


#### If you get an error:

- Check if you can execute `docker ps` without sudo. If not then do the following:
	1. `sudo groupadd docker`
	2. `sudo gpasswd -a $USER docker`
	3. `sudo service docker restart`
	4. `sudo chown $USER /var/run/docker.sock`
	5. If docker-compose still doesn't work then try restarting your computer
- If you can run `docker-compose up -d` but you get the error : `Error starting userland proxy: listen tcp4 0.0.0.0:3306: bind: address already in use`. The port used by the container may already be used so:
	1. Open `parking-app/docker-compose.yml`
	2. Change the port at the line `- "3307:3307"` in `ports` in `db` to another (`"3308:3308"` for instance)
	3. `docker-compose up -d`

## Execution

### To open the website

Go to http://localhost:8000/ in your web browser

### To edit the database (admin)

1. Run `docker ps`
2. Get the CONTAINER ID (1st column) of the mysql IMAGE
3. Replace CONTAINER_ID with what you got in (2.), in `docker exec -ti CONTAINER_ID bash` and run it
4. Enter `mysql -p`
6. Enter the password that was defined in the environment variable MYSQL_ROOT_PASSWORD in docker-compose.yml
7. Enter `use usersdata;`
8. Enter the queries you want to do

## Authors

- Romain Barré
- Yann Etrillard
- David Kusmider
- Robin Meneust
- Baptiste Ruellan
