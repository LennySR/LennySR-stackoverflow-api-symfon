"# LennySR-stackoverflow-api-symfony" 

Estructura del Proyecto
src/
Controller/: Contiene el controlador StackOverflowController, que maneja las solicitudes de la API.
Entity/: Define las entidades Query y Question, que representan una consulta realizada y sus preguntas asociadas.
Repository/: Incluye los repositorios de las entidades para consultas avanzadas a la base de datos.
Service/: Contiene el servicio StackOverflowApiService, encargado de interactuar con la API de Stack Overflow.
config/: Configuraciones generales de Symfony, como la conexión a la base de datos y rutas.
migrations/: Almacena las migraciones de base de datos para crear las tablas necesarias.
public/: Carpeta pública donde se encuentra el archivo index.php para las solicitudes de entrada.
Configuración del Proyecto


1. Clonar el Repositorio

Clonar el repositorio de GitHub en tu máquina local:
git clone https://github.com/tu_usuario/stackoverflow-api-symfony.git
cd stackoverflow-api-symfony

2. Instalar Dependencias

Instala las dependencias de PHP usando Composer:
composer install

3. Configurar Variables de Entorno

Copia el archivo .env y actualiza las variables necesarias:
cp .env .env.local
En .env.local, configura la conexión a la base de datos.
DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:3306/stackoverflow_db"


4. Crear la Base de Datos y Ejecutar Migraciones

Ejecuta los siguientes comandos para crear la base de datos y las tablas necesarias:
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Iniciar el Servidor de Desarrollo

Inicia el servidor de desarrollo de Symfony:
symfony server:start
La API estará disponible en http://127.0.0.1:8000.

Uso de la API
Endpoint Principal
GET /api/questions

Este endpoint permite realizar consultas de preguntas en Stack Overflow usando los siguientes parámetros:

tagged (obligatorio): etiqueta por la que se desea filtrar.
fromdate (opcional): fecha de inicio del rango (timestamp o formato Y-m-d).
todate (opcional): fecha de fin del rango (timestamp o formato Y-m-d).
Ejemplo de Uso:

bash
Copiar código
curl -X GET "http://127.0.0.1:8000/api/questions?tagged=symfony&fromdate=2023-01-01&todate=2023-01-31"


Respuesta:
La API devolverá un JSON con las preguntas obtenidas, con un formato similar a este:

json
[
  {
    "title": "Cómo instalar Symfony",
    "creation_date": "2023-01-15 10:30:00",
    "body": "Descripción de la pregunta...",
    "tags": "symfony"
  },
  {
    "title": "Errores al usar Doctrine en Symfony",
    "creation_date": "2023-01-20 11:45:00",
    "body": "Descripción de la pregunta...",
    "tags": "symfony"
  }
]

Consulta en la Base de Datos
El proyecto almacena cada consulta única en la tabla Query, junto con las preguntas relacionadas en la tabla Question. Para acceder y consultar los datos de la base de datos, puedes usar el cliente de tu elección (phpMyAdmin, MySQL Workbench, etc.) o conectarte desde la línea de comandos:
mysql -u usuario -p -D stackoverflow_db
Dentro de la base de datos, puedes ejecutar consultas SQL para ver las preguntas almacenadas y sus relaciones con las consultas realizadas.

Consejos y Notas Adicionales
Optimización de Consultas: El uso de índices en los campos tagged, fromDate y toDate de la tabla Query optimiza las búsquedas por estos parámetros.
Manejo de Errores: La API devuelve respuestas de error en caso de parámetros faltantes o errores en la conexión con la API de Stack Overflow.
Eficiencia: Si la consulta ya existe en la base de datos, se evitan llamadas adicionales a la API de Stack Overflow, devolviendo los datos almacenados.
Código: La lógica de conexión con la API está separada en un servicio (StackOverflowApiService) para mantener el código organizado y facilitar su prueba.
