# Symfony Order Dispatcher

Welcome to **Symfony Order Dispatcher**, an application developed in Symfony that uses a command-based approach to create orders, dispatch events to queues, and process those queues efficiently.

## Getting Started

### Clone the Repository

To get started, clone the repository using the following command:

```bash
git clone git@github.com:wergh/symfony-order-dispacher.git  
```
### Setup and Execution  

Once the project is downloaded to your local environment, you need to start it. The entire project is **dockerized**, making setup simple and efficient.  

#### Windows  

1. **Download and Install Docker Desktop**  
   - You need to create an account on Docker: [Docker Desktop](https://www.docker.com/products/docker-desktop/)  
   - Once registered and logged in, download and install Docker Desktop.  

2. **Run Docker Desktop**  
   - After installation, launch **Docker Desktop** and keep it running while using the application.  

3. **Open a Terminal**  
   - Open a console, such as **Windows PowerShell**.  
   - Navigate to the project's root directory.  

4. **Run the following command:**  

```bash
docker-compose up -d --build
```

This command will start all the necessary services to run the application:
- **Nginx**
- **RabbitMQ**
- **MySQL**
- **A Linux VM** where the application files will be hosted

Additionally, Docker will start a **supervisor** that ensures the message queues are processed as data is added to them.

#### Linux

1. **Install Docker and Docker Compose**
    - On Debian/Ubuntu-based distributions:

   ```bash
   sudo apt update
   sudo apt install docker.io docker-compose -y  
   ```

   - On RedHat-based distributions (Fedora, CentOS):  

   ```bash
   sudo dnf install docker docker-compose -y  
   ```

2. **Start and enable Docker**

   ```bash
   sudo systemctl start docker
   sudo systemctl enable docker  
    ```

3. **(Optional) Add your user to the Docker group**  
   To avoid using `sudo` for Docker commands:

   ```bash
   sudo usermod -aG docker $USER
   newgrp docker  
   ```

4. **Clone the repository and navigate to the directory**

   ```bash
   git clone git@github.com:wergh/symfony-order-dispacher.git
   cd symfony-order-dispacher  
   ```

5. **Start the containers with Docker Compose**

   ```bash
   docker-compose up -d --build  
   ```

This will start all the required services (**Nginx, MySQL, RabbitMQ, Supervisor, etc.**) just like in Windows.

### Stopping the Containers

- To stop the containers, simply run:

  ```bash
  docker-compose down  
  ```

- If you also want to remove databases and any associated data, add the `-v` flag:

  ```bash
  docker-compose down -v  
  ```
### Initializing the Application

#### First Initialization (or Initialization after a `down -v`)

1. **Run Migrations**  
   The first step is to execute the application migrations to create the necessary database tables. You can do this by running:

```bash
docker-compose exec app php bin/console doctrine:migrations:migrate  
```

2. **Validate the Schema**  
   You can validate the schema with the following command:  

```bash
docker-compose exec app php bin/console doctrine:schema:validate  
```

3. **Deploy Seeders**  
   If you want to add some test data to the database, you can do so with the following command:  

```bash
docker-compose exec app php bin/console doctrine:fixtures:load  
```

This will create three clients and three products for you to start working with.  

## Commands

The application provides the user with several commands to interact with it:

### Create Client Command

This command is used to create new clients in the database. You can run it with:  

```bash
docker-compose exec app php bin/console app:create-client  
```

### Create Product Command
This command is used to create new products in the database. You can run it with:
```bash
docker-compose exec app php bin/console app:create-product  
```
### Update Stock Command
This command is used to update the stock of a product in the database. You can run it with:
```bash
docker-compose exec app php bin/console app:stock-update  
```

### Create Order Command

This command triggers all the functionalities of the application, with the other three being simple auxiliary commands.

```bash
docker-compose exec app php bin/console app:create-order  
```

With this command, orders are added to the database, which automatically triggers an event that adds a message to a queue to be processed.  
The supervisor will pick up that message, and the appropriate consumer will be ready to process it. Once done, it will send another message to a different queue with the result of the order, so another consumer can collect it and process the notification to the user (which is currently faked as a log).  

In the event of any queue failure, the system is configured to perform 3 retries, and if it fails after those attempts, the message will be moved to a failed events queue for manual review and processing. In any case, the user will be notified that their order has been canceled due to a platform error.  

Error or exception notifications can be controlled through an enabled **Sentry** instance.  


