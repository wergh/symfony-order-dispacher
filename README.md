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

### Testing

All operations performed in the application can be tested through unit tests.

To verify everything is working correctly, you can run the following command:

```bash
docker-compose exec app php bin/phpunit  
```

## Explicación del ejercicio

Para la explicación del ejercicio voy a pasar al castellano. La primera parte esta en inglés porque considero que es el idioma
correcto para poner en los readme de los repositorios, pero en harás de la comodidad voy a escribir esta parte en castellano
para que sea más accesible a cualquier persona.
Para este ejercicio he elegido un patrón de diseño DDD con arquitectura Hexagonal y intentando respetar siempre los principios
SOLID

### Capa de Dominio

En la capa de dominio tenemos, agrupadas por carpetas, los 3 grandes grupos que componen la aplicación:
- Clientes
- Productos
- Pedidos

Y a mayores un grupo más llamado Shared para todos aquellos recursos compartidos dentro de la capa de dominio.

#### Client - Capa de Dominio

Cliente sea posiblemente la Entidad más sencilla dado que solo he asignado dos atributos a la entidad, con el objetivo de facilitar los testeos
El cliente solo tiene nombre y apellidos, y a mayores de la entidad simplemente se ha creado una clase de Factory para facilitar
los seeders de la base de datos y una Interfaz para su repositorio.

#### Product - Capa de Dominio

El producto también es una Entidad sencilla, con algunos atributos más que la entidad de cliente, para hacer una simulación
un poco más veraz, ya que incluye precio e impuestos. A mayores se ha añadido el atributo stock como un entero para la verficación
de sí cuando se ejecuta un pedido queda stock o no. Este campo en un caso real posiblemente fuese un atributo computado obtenido de
almacenes, centros logísticos etc... más que un campo de la base de datos, pero para esta aplicación de prueba nos sirve.

A mayores de la entidad tenemos también un método Factory para los seeders, la interfaz del repositorio y una excepción propia de esta entidad
que es la excepcion de Stock insuficiente

#### Order - Capa de Dominio

Los pedidos tienen una capa de dominio ligeramente más compleja. Para empezar la entidad no solo tiene atributos planos.
Para los conceptos de un pedido o las líneas, de un pedido se han creado un ValueObject dado que refleja mejor lo que son.
Una vez un pedido se crea los conceptos del mismo no pueden modificarse, y esa propiedad de inmutabilidad los convierte 
en Value Objects. También se ha generado un Enum, ya que PHP nos los brinda desde su versión 8, para reflejar los estados 
del pedido dondne se ha añadido un estado más de los indicados en el enunciado del pedido que es el estado de Fallido (failed)
que describiremos cuando hablemos de la funcionalidad.
A mayores de estos dos atributos tenemos al igual que las anteriores la interfaz del repositorio y en este caso los Eventos.
Dado que la creación de las orders es lo que dispara toda la lógica de nuestra aplicación aquí tenemos los eventos que se disparan
en esos momentos. Cocretamente tenemos el OrderCreatedEvent y el OrderStatusUpdatedEvent que se disparan cuando un pedido se crea
o cuando el estado de un pedido cambia.

#### Shared - Capa de Dominio
La última carpeta es la de shared donde se encuentran la interfaz de evento de dominio, Interfaces generales para la aplicación
y excepciones generales 

### Capa de Aplicación

