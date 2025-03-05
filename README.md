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

1. **Install the composer**
The first step is install the composer packages. Run the command

```bash
docker-compose exec app composer install
```

2. **Run Migrations**  
   The first step is to execute the application migrations to create the necessary database tables. You can do this by running:

```bash
docker-compose exec app php bin/console doctrine:migrations:migrate  
```

3. **Validate the Schema**  
   You can validate the schema with the following command:  

```bash
docker-compose exec app php bin/console doctrine:schema:validate  
```

4. **Deploy Seeders**  
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
docker-compose exec app php bin/console app:update-stock  
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
### Application Flow Variations

Two environment variables have been implemented in the `.env` file, which slightly modify the way the application works:

#### Stock Control During Order Creation

The first variable enables or disables stock control when creating an order.
- When set to **true**, the system will always verify that there is enough stock available at the moment of order creation.
    However, this does not remove the second stock verification performed during order processing, as this step is unavoidable.
- When set to **false** the system will skip the verification of the stock on the command.

This option has been implemented to improve testability, allowing orders to be created with a stock quantity greater than the available amount. This enables simulation and validation of the application's flow in cases where stock is insufficient.

#### Forcing Errors in Order Processing

The second variable allows forced errors during order processing. It accepts values between **0 and 100**:
- **0** means no forced failures.
- **100** means all order processing attempts will fail.
- **50** means that **50% of the orders will fail**, while the remaining 50% will be processed successfully.

This variable has been added to intentionally introduce failures in queue processing and observe how the application behaves when the message queues fail for any reason. This enables the simulation of error scenarios within the queuing system, ensuring that the application correctly handles these situations and maintains a stable workflow.

These variables allow for more flexible testing, simulating specific conditions in the application flow to ensure its robustness under different scenarios.

#### Important Note

If you modify any of these environment variables in the `.env` file, you must rebuild the Docker containers for the changes to take effect. Run the following command:

```bash
docker-compose up -d --build 
```

## Explicación del ejercicio

Para la explicación del ejercicio voy a pasar al castellano. La primera parte está en inglés porque considero que es el idioma
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
un poco más veraz, ya que incluye precio e impuestos. A mayores se ha añadido el atributo stock como un entero para la verificación
de sí cuando se ejecuta un pedido queda stock o no. Este campo en un caso real posiblemente fuese un atributo computado obtenido de
almacenes, centros logísticos, etc, más que un campo de la base de datos, pero para esta aplicación de prueba nos sirve.

A mayores de la entidad tenemos también un método Factory para los seeders, la interfaz del repositorio y una excepción propia de esta entidad
que es la excepción de Stock insuficiente

#### Order - Capa de Dominio

Los pedidos tienen una capa de dominio ligeramente más compleja. Para empezar la entidad no solo tiene atributos planos.
Para los conceptos (o las líneas) de un pedido, se han creado un ValueObject dado que refleja mejor lo que son.
Una vez un pedido se crea los conceptos del mismo no pueden modificarse, y esa propiedad de inmutabilidad los convierte 
en Value Objects. También se ha generado un Enum, ya que PHP nos los brinda desde su versión 8, para reflejar los estados 
del pedido. Se ha añadido un estado más de los indicados en el enunciado del pedido, que es el estado de Fallido (failed)
que describiremos cuando hablemos de la funcionalidad.
A mayores de estos dos atributos tenemos al igual que las anteriores la interfaz del repositorio y en este caso los Eventos.
Dado que la creación de las orders es lo que dispara toda la lógica de nuestra aplicación aquí tenemos los eventos que se disparan
en esos momentos. Concretamente, tenemos el OrderCreatedEvent y el OrderStatusUpdatedEvent que se disparan cuando un pedido se crea
o cuando el estado de un pedido cambia.

#### Shared - Capa de Dominio
La última carpeta es la de shared donde se encuentran la interfaz de evento de dominio, Interfaces generales para la aplicación
y excepciones generales 

### Capa de Aplicación

En la capa de aplicación, al igual que en la capa de dominio, existen tres grandes carpetas donde se agrupan los archivos según los tres principales grupos: Producto, Cliente y Order.

Cada una de estas carpetas contiene los siguientes elementos clave:

- **DTOs** (Data Transfer Objects):
Los DTOs actúan como un sistema de transporte de datos entre la capa de infraestructura y la capa de aplicación, evitando así instanciar directamente la capa de dominio desde infraestructura. Además, ayudan a acotar los datos que se envían en la request hacia la capa de aplicación.

   En este caso en particular, dado que los comandos han sido diseñados específicamente para los servicios que necesitamos, no existen datos extra en los DTOs. Sin embargo, en un caso real siempre utilizaríamos DTOs para transportar los datos de manera estructurada.

- **Servicios:**
Los servicios cumplen la función de puente entre la capa de infraestructura y la capa de aplicación. Son los encargados de recibir las peticiones desde infraestructura y delegarlas a donde sea necesario dentro de la aplicación.

-  **Casos de Uso:**
En los casos de uso seguimos estrictamente los principios SOLID, asegurándonos de que cada uno tenga una única responsabilidad, la cual queda claramente reflejada en su nombre. Esto permite mantener una arquitectura limpia, modular y fácilmente escalable.

-  **Validadores**:
Se han añadido validadores como una segunda capa de validación, aunque ya existe una validación previa en los comandos sobre los datos que reciben los DTOs. Su propósito es garantizar que todos los datos que se intentan pasar a los constructores de las entidades tengan el formato correcto.

-  **Sequencer** para Orders:
En el caso de los pedidos, se ha añadido un sequencer debido a que, al momento de procesar un pedido, se deben ejecutar varios casos de uso en secuencia, como buscar el pedido, recorrer los conceptos, buscar los productos, comprobar stocks, etc.

   Para evitar que un servicio tenga que instanciar varios casos de uso directamente, el sequencer se encarga de orquestar la ejecución de cada caso de uso en el pedido correcto, asegurando un flujo estructurado y mantenible.

### Capa de Infraestructura
La capa de infraestructura contiene todos los elementos encargados de interactuar con el mundo exterior fuera de nuestra aplicación. Aquí gestionamos la comunicación con:

-  Base de datos
-  Colas de RabbitMQ
- Logger (utilizando Monolog)
 - Sentry
-  Otros servicios externos
  
Para organizar estos elementos, la capa de infraestructura se divide en varias carpetas:

**Command**: Contiene todos los comandos de la aplicación. Además, incluye una clase abstracta, AbstractCommand, que implementa funcionalidades generales para todos los comandos.

**DataFixtures**: Contiene los datos iniciales para la base de datos, los seeders necesarios para cargar información de ejemplo.

**Logging**: Implementa la interfaz de Logger definida en la capa de dominio, utilizando Monolog para la gestión de logs.

**Messenger**: Gestiona la interacción con las colas de RabbitMQ, incluyendo los consumidores de las colas y un listener que reenvía mensajes a una cola de salida en caso de fallar tres veces.

**Monitoring**: Contiene la integración con Sentry para el monitoreo de errores y eventos dentro de la aplicación.

**Persistencia/Doctrine**: Contiene las implementaciones de las interfaces de cada entidad definida en el dominio.
Incluye un listener que detecta cambios en la base de datos relacionados con las pedidos, disparando eventos cuando sea necesario.
Esta estructura permite una organización clara de los componentes de infraestructura y facilita la integración con servicios externos sin acoplar la lógica de negocio a estos elementos.

## ¿Cómo funciona?

El flujo de la aplicación es bastante sencillo. Dejando de lado los comandos auxiliares como crear cliente, crear producto y actualizar stock, que simplemente actualizan la base de datos, el comando de crear orders tiene un funcionamiento un poco más extenso.

Concretamente, el comando de crear orders sigue estos pasos:

1. Creación de la Order:

   - En primera instancia, el comando añade una nueva instancia de la entidad Order en la base de datos.
2. Disparo del evento OrderCreated:

   - Cuando se crea la instancia en la base de datos, se dispara el evento OrderCreated.
   - Esto sucede porque Order tiene un listener de Doctrine que monitorea los cambios en la base de datos. 
3. Listener de Doctrine:
   - El listener está configurado para ejecutarse con el evento persist en Doctrine específicamente para la entidad Order.
   - Cuando este evento se dispara, el listener ejecuta la lógica correspondiente para procesar la order.
4. Envío del mensaje a RabbitMQ:
   - En el momento en que se dispara el evento, se envía un mensaje a RabbitMQ a través del Messenger de Symfony.
   - Este mensaje se añade en la cola ORDER_CREATED_QUEUE e incluye:
     - ORDER ID
     -   CLIENT ID
     - Fecha y hora de la order
5. Procesamiento del mensaje por el Supervisor:

   - Desde el momento en que se levanta el worker, el Supervisor está activo, revisando constantemente la cola.
   - Si se recibe un mensaje, se procesa automáticamente.
6. Ejecución del Consumer de Symfony:
   - Symfony tiene configurados los Consumers que consumen cada cola.
   - Para esta cola en particular, el mensaje es recibido por el Consumer de "Procesar la Order".
   - Este Consumer llama al servicio, que a su vez invoca al Sequencer, donde se ejecutan todos los casos de uso asociados al procesamiento de la order.
7. Ejecución del Sequencer:

   - El Sequencer irá llamando a todos los casos de uso necesarios para procesar una order.
   - Primero buscará la instancia de la Order.
   - Luego, recorrerá cada uno de los conceptos y obtendrá los productos asociados a ellos.
   - Para cada producto, verificará si hay stock suficiente para procesar el pedido.
   - En caso de que haya stock suficiente, lo descontará del inventario.
8. Finalización de la order:
   - Cuando haya terminado de procesar todos los conceptos, el estado de la order se marcará como aceptado.
9. Excepción por stock insuficiente:
   - Si en algún momento se detecta que un producto no tiene el stock necesario, la revisión de productos se detendrá y se lanzará una excepción de stock insuficiente.
   - Esta excepción tiene dos efectos importantes:
     - Se realiza un rollback de todo lo realizado hasta ese momento, asegurando que los productos procesados antes del que no pudo ser procesado no modifiquen su stock, evitando pedidos incompletos en la base de datos.
     - El estado de la order se actualizará a cancelado.
10. Disparo de eventos en el cambio de estado:

    - En el momento en el que el estado de la order pasa a aceptada o cancelada, se dispara un segundo evento dentro del listener de las orders.
    - Concretamente, se disparan dos eventos:
      - Pre-update: Este evento guarda el estado de la order antes del guardado.
      - Post-update: Este evento comprueba si el estado de la order ha cambiado.
    - En el ejercicio actual, esto no tiene mucho sentido, ya que la order solo se actualiza para cambiar su estado. Por lo tanto, si la order ha cambiado, se considera que siempre habrá un cambio de estado.
    - Sin embargo, en una implementación futura o en un caso real de uso, podría ser que se modifique la order, pero que el estado no cambie. En ese caso, este patrón previene que se lance el evento innecesariamente.
11. Notificación de cambio de estado:
    - Cuando el estado de la orde3 cambia, se añade un mensaje en la cola ORDER_STATUS_CHANGED_QUEUE.
    - El mensaje incluye:
      - ORDER ID
      - Un mensaje personalizado en función del nuevo estado (aceptado, cancelado, fallido).
    - El Supervisor está configurado para procesar esta cola y, cuando se recibe un mensaje, Symfony redirige el mensaje al consumer correspondiente.
    - El trabajo de este consumer y del servicio asociado es notificar al usuario.
    - Si el estado es aceptado, el servicio envía una notificación de éxito, para este ejemplo, registrando un log con LOGGER indicando que el pedido ha sido procesado.
    - Si el estado es cancelado, se notifica al usuario que el pedido ha sido cancelado.
    - Además, se ha añadido un tercer estado, fallido, que se utilizará en el caso de que alguna de las colas de procesamiento falle.

Este flujo asegura que las orders se procesen de manera eficiente y consistente, gestionando las excepciones de stock de forma que se garantice la integridad de los datos en la base de datos y notificando al usuario del resultado de su pedido.

### ¿Cómo funciona en caso de error en las colas?

En el caso de que cualquiera de las dos colas falle, debido a un problema como que RabbitMQ esté caído o por cualquier otra excepción, fuera del motivo de que no haya stock, la cola pasará a estar fallida.

El sistema de Messenger está configurado para que intente procesar el mensaje tres veces. Si después de tres intentos el procesamiento sigue fallando, el mensaje se enviará a una cola de fallos, específicamente _failed_queue_, donde quedará almacenado para su posterior revisión y procesamiento manual.

Además, hemos implementado un listener para el evento propio de Symfony que maneja el fallo de una cola. Este listener se activa cuando ya no hay más intentos de reintento disponibles. En este caso, el listener cambia automáticamente el estado de la order a fallido.

Por último, también se dispara el evento de cambio de estado, notificando al usuario que su pedido ha fallado debido a motivos técnicos y que, por favor, lo intente de nuevo.

Este mecanismo garantiza que los pedidos con errores fuera del control de stock sean gestionados de forma adecuada y el usuario reciba una notificación clara sobre el estado de su pedido.

Además de la gestión de las colas fallidas o cualquier otra excepción que pueda ocurrir durante el uso del programa, todo está monitorizado a través de **Sentry** para que siempre haya notificaciones de cualquier error que se pueda producir a lo largo del procesamiento de la aplicación.

Esto garantiza que cualquier incidencia técnica o fallo en el sistema sea detectado de inmediato y se pueda tomar acción para resolverlo rápidamente.

## Variaciones del flujo de la aplicación
Se han implementado dos variables en el archivo de entorno .env que modifican ligeramente la forma de trabajar de la aplicación:

1. Control de stock en la creación del pedido:
La primera variable activa o desactiva el control de stock a la hora de realizar un pedido. Cuando su valor es true, siempre se verificará que el producto tenga stock suficiente al momento de crear la order. Sin embargo, esto no elimina la segunda comprobación que se realiza durante el procesamiento de la order, ya que esta es inevitable. 
En el caso de ser false, simplemente ignoraremos la verificación de stock en el comando de crear orders, de manera que se podrán pedir productos por encima de su stock actual.
Esta opción ha sido implementada para mejorar la testabilidad, permitiendo crear orders con un stock superior al disponible para poder simular y comprobar el flujo de la aplicación en caso de que el stock sea insuficiente.

2. Forzar errores en el procesamiento de pedidos:
La segunda variable permite forzar errores en el procesamiento de los pedidos. Esta variable tiene un valor entre 0 y 100:

   - 0 significa que no habrá fallos forzados.
   - 100 indica que todos los procesamientos fallarán.
   - Un valor de, por ejemplo, 50 haría que el 50% de los procesamientos fallaran y el otro 50% se procesaran correctamente.
     
   Esta variable se ha añadido para poder forzar fallos en el procesamiento de las colas y ver el flujo de la aplicación en caso de que falle el procesamiento de las colas por cualquier motivo. De este modo, se pueden simular escenarios de error en el sistema de colas, lo que permite probar cómo la aplicación maneja estas situaciones y asegura que todo el flujo de trabajo se gestione adecuadamente.

Estas variables permiten realizar pruebas más flexibles, simulando condiciones específicas en el flujo de la aplicación para asegurar su robustez en diferentes escenarios.

## Consideraciones finales
He aplicado el diseño DDD, arquitectura externa y seguido firmemente los principios SOLID. Sin embargo, es importante señalar que, en la comunidad, existe cierta controversia sobre en qué punto deben aplicarse estos enfoques. En este caso, he aplicado lo que considero la forma más razonable de hacerlo. Esto no quiere decir que sea la única forma correcta o incorrecta, sino que es la manera en la que considero que debe construirse este sistema.

Esto no implica que, si trabajamos juntos en el futuro, debamos seguir este mismo enfoque. Por supuesto, me adaptaría al sistema que se utilice en vuestra empresa. Lo que quiero destacar es que he querido aportar mi enfoque personal a esta arquitectura, y con ello espero ofrecer una base flexible que permita ajustarse a otros contextos y necesidades.

Para ilustrar mi enfoque, a continuación pongo un ejemplo de cómo lo he aplicado:

Actualmente, para crear las tablas de la base de datos, utilizo el sistema de anotaciones en las entidades. Sin embargo, desde un punto de vista estricto de la arquitectura hexagonal, esta práctica no es la más adecuada. Esto se debe a que Doctrine, para crear las tablas, consulta las entidades, las cuales se encuentran en la capa de dominio. Esto supone una violación de los principios de la arquitectura hexagonal, ya que la capa de dominio no debe depender de detalles de infraestructura, como la persistencia de datos.

No obstante, en el contexto de Symfony, esta es la forma convencional de crear las tablas de la base de datos, lo que genera un pequeño dilema entre seguir las prácticas recomendadas del framework y adherirse estrictamente a la arquitectura hexagonal.

¿Cómo se podría mejorar para ajustarse mejor a la arquitectura hexagonal?
Para respetar mejor los principios de la arquitectura hexagonal, una opción sería eliminar las anotaciones de las entidades en la capa de dominio y trasladarlas a otros archivos dentro de la capa de infraestructura. Estos archivos serían los encargados de reflejar las entidades, permitiendo que Symfony busque las anotaciones en estos archivos en lugar de en las entidades directamente. De este modo, se lograría una separación más clara entre el dominio y la infraestructura, mejorando el respeto por la arquitectura hexagonal.

Sin embargo, en mi opinión, esta solución choca un poco con el flujo de trabajo natural de Symfony, que está diseñado para gestionar la creación de tablas directamente desde las entidades, por lo que se perdería cierta simplicidad y agilidad en el desarrollo.
