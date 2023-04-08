# parking-app

## About

A web app that searches for nearby parking lots (in France)

## Dependencies

- Docker
- Docker Compose

## Installation


### Docker

### Docker Compose

### Build containers

#### Steps

Run the following command in the root of this project (parking-app/): `docker-compose up -d`


#### If you get an error:

<ul> 
	<li>
		Check if you can execute <code>docker ps</code> without sudo. If not then do the following:
		<ol>
			<li><code>sudo groupadd docker</code></li>
			<li><code>sudo gpasswd -a $USER docker</code></li>
			<li><code>sudo service docker restart</code></li>
			<li>Restart your computer</li>
			<li><code>docker-compose up -d</code></li>
		</ol>
	</li>
	<li>
		If you can run <code>docker-compose up -d</code> but you get the error :<br>
		<code style="color:red">Error starting userland proxy: listen tcp4 0.0.0.0:3306: bind: address already in use</code><br>
		The port used by the container may already be used so:
		<ol>
			<li>Open parking-app/docker-compose.yml</li>
			<li>
				Change the port at the line <code>- "3307:3307"</code> in <code>ports</code> in <code>db</code> to another (<code>3308:3308</code> for instance)
			</li>
			<li>
				<code>docker-compose up -d</code>
			</li>
		</ol>
	</li>
</ul>

## Execution

### To open the website

Go to http://localhost:8000/ in your web browser

### To edit the database (admin)

1. Run <code>docker ps</code>
2. Get the CONTAINER ID (1st column) of the mysql IMAGE
3. Replace CONTAINER_ID with what you got in (2.), in <code>docker exec -ti CONTAINER_ID bash</code> and run it
4. Enter <code>use usersdata;</code>
5. Enter the queries you want to do

## Authors

- Romain Barré
- Yann Etrillard
- David Kusmider
- Robin Meneust
- Baptiste Ruellan