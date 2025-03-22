# 🍏📦 Control de Pedidos para Frutería con Múltiples Sucursales 🏪  


## 📖 Descripción  
Este proyecto permite gestionar los pedidos de una frutería con múltiples sucursales de manera eficiente. Su objetivo principal es ayudar a los encargados de tienda y al administrador a controlar las cantidades necesarias de productos para cada sucursal y el total consolidado, está diseñado para ser totalmente responsivo, asegurando una experiencia fluida tanto en dispositivos móviles como en navegadores de pantalla pequeña. Además, incluye un modo oscuro para una mejor experiencia visual en entornos con poca luz.
### 🔹 **Características principales:**  
✅ **Gestión de pedidos:** Visualización de pedidos por tienda y por fecha.  
✅ **Importación de datos:** Permite importar productos y tiendas desde archivos Excel.  
✅ **Control de usuarios y permisos:** 
   - 📌 *Encargados de tienda:* Pueden registrar pedidos y ver reportes solo de sus tiendas.  
   - 🔑 *Administrador:* Tiene acceso a los pedidos de todas las sucursales y puede gestionar permisos.  
✅ **Edición de pedidos:** Los pedidos se realizan diariamente y pueden ser editados si aún no han sido registrados. 
Este sistema garantiza un control detallado y accesible de los pedidos para mejorar la gestión de abastecimiento. 📊✨  


## 🚀 Estado del Proyecto  
✅ **Estado actual:** Proyecto finalizado con opciones de mejora.  
🔄 **Mejoras futuras planeadas:**  
   - 📝 **Módulo de auditoría**: Registrar cambios en pedidos y usuarios, indicando quién los realizó.  
   - 📊 **Exportación a Excel**: Generar reportes exportables, facilitando el análisis de datos.  
Este sistema está operativo y puede evolucionar para mejorar la gestión de pedidos. 🚀  

## 🛠 Requisitos Previos  
Este proyecto utiliza diversas tecnologías y dependencias. Asegúrate de contar con el siguiente entorno de desarrollo antes de la instalación.

### 🔹 **Stack Tecnológico**  
- ⚙️ **Backend:** 
    [Laravel 11](https://laravel.com/) (PHP)
- 🎨 **Frontend:** 
    [Vue.js](https://vuejs.org/) + [Inertia.js](https://inertiajs.com/)  
- 🗄 **Base de Datos:** 
    [MySQL](https://www.mysql.com/)  
- 🚀 **Control de Versiones & CI/CD:** 
    [Git](https://git-scm.com/) / [GitHub](https://github.com/)  

### 🔹 **Dependencias Necesarias**  
Asegúrate de tener instaladas estas herramientas antes de ejecutar el proyecto:  
- 📦 [Node.js](https://nodejs.org/) y [npm](https://www.npmjs.com/) (para manejar paquetes)  
- 📜 [Composer](https://getcomposer.org/) (para gestionar dependencias de PHP)  
- 📊 [Laravel Excel](https://laravel-excel.com/) (para importar/exportar archivos Excel)  
- ⚡ [Vite](https://vitejs.dev/) (como bundler para Vue)  
- 🎨 [Tailwind CSS](https://tailwindcss.com/) (para estilos rápidos y responsivos)  

### 🔹 **Configuraciones**  
💡 Antes de continuar, asegúrate de tener instalados:  
- 📦 [Node.js](https://nodejs.org/) (incluye npm)  
- 🐘 [PHP 8.2+](https://www.php.net/) con las siguientes extensiones habilitadas:  
  - `mbstring`
  - `openssl`
  - `ext-fileinfo`
  - `ext-zip` (necesario para Laravel Excel)  
- 🗄 [MySQL 8.0+](https://www.mysql.com/)  
- ⚙️ [Composer](https://getcomposer.org/)  
- 🛠 Un entorno de desarrollo como:
    [Laragon](https://laragon.org/) (Recomendado)
    [XAMPP](https://www.apachefriends.org/)


## 🔧 Instalación  
Sigue estos pasos para instalar y configurar el proyecto correctamente.  
### 📌 **1. Requisitos previos**  
Explicados en el paso anterior ☝🏼️☝🏼️

### 🚀 **2. Clonar el repositorio**  
git clone https://github.com/EstebanSahid/ElHiperTienda.git

cd proyecto

### 📦 3. Instalar dependencias
# Instalar dependencias de Laravel
composer install  

# Instalar dependencias de Vue y Vite
npm install

### ⚙️ 4. Configurar variables de entorno
# Configurar variables de entorno
Puedes ver la estructura en .env.example

### 🏗 5. Crear la base de datos y ejecutar migraciones
php artisan migrate

### 🔑 6. Generar clave de aplicación y enlazar almacenamiento
# Generar clave unica para el proyecto
php artisan key:generate

# Crear enlace simbolico para acceder a los archivos de imagen o reportes
php artisan storage:link

### ⚡ 7. Ejecutar el servidor de desarrollo
# Para iniciar el frontend (Vite + Vue):
npm run build

# Para iniciar el backend (Laravel):
php artisan serve


## 📝 Uso  
### 🏁 **Primeros pasos**  
1. 📌 **Registro del administrador:**  
   - Como no hay usuarios pre-registrados, el primer usuario que se registre tendrá permisos de *administrador total*.  
   - Ingresa un correo y contraseña para crear la cuenta inicial.  
2. 🏪 **Carga de datos iniciales:**  
   - Agrega al menos **una tienda** y algunos **productos**.  
   - 📌 *Los productos no se asignan a tiendas específicas; cada sucursal puede solicitar los productos que necesite.*  

### 🛒 **Gestión de pedidos**  
1. **Registrar una orden:**  
   - Busca productos por **código o nombre**.  
   - Ingresa la **cantidad** y selecciona la **unidad de medida** (unidades, kg, cajas, etc.).  
   - ⚙️ *Las unidades de medida son totalmente configurables.*  
2. **Reportes y consultas:**  
   - 📊 Filtra pedidos por **tienda y fecha** para visualizar las solicitudes realizadas.  
   - 📄 **Exportación disponible:**  
     - **📌 PDF** para generar reportes detallados.  
     - **(Próximamente: Exportación a Excel 📊)**  


## 👨‍💻 Autor  
Este proyecto fue inicializado utilizando **Inertia.js** como esqueleto base y adaptado para las necesidades del proyecto.  

Este es mi **primer proyecto** desarrollado desde cero con frameworks y tecnologías web (anteriormente lo hacía todo con vanilla), y lo estoy añadiendo a mi **portafolio** para mostrar mi experiencia y crecimiento. ¡Estoy muy emocionado de compartirlo! 😄🚀  

Aunque tengo **experiencia profesional**, quice realizar un proyecto sencillo para validar mis conocimientos, este es **mi primer proyecto** desarrollado desde cero utilizando **frameworks modernos** como Vue.js y Laravel. Este proyecto me ha permitido adentrarme más en el ecosistema actual de desarrollo web y lo estoy añadiendo a mi **portafolio** para mostrar mi evolución y crecimiento. ¡Estoy muy emocionado de compartirlo! 😄🚀

### 🔹 **Desarrollado por:**  
Esteban Sahid
Github: https://github.com/estebanSahid
LinkedIn: https://www.linkedin.com/in/esteban-sahid/
