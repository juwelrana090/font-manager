
# Laravel API Backend

This repository contains the source code for a Laravel API backend that handles font management and groups. This guide will walk you through the process of setting up the project, including installation instructions and how to deploy it to a VPS using a control panel.

## Prerequisites

- PHP >= 8.3.10
- Composer
- MySQL or PostgreSQL
- Nginx or Apache web server
- Git
- Node.js and npm (for frontend assets if needed)

## Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

### 2. Install Dependencies

Make sure you have [Composer](https://getcomposer.org/download/) installed, then run:

```bash
composer install
```

If you have frontend assets (optional), you may also need to run:

```bash
npm install && npm run dev
```

### 3. Environment Configuration

Create a copy of the `.env` file:

```bash
cp .env.example .env
```

Update the following environment variables in the `.env` file:

- `DB_DATABASE` – your database name
- `DB_USERNAME` – your database username
- `DB_PASSWORD` – your database password

You can also set up other configurations such as `APP_NAME`, `APP_URL`, `MAIL_`, and any other variables that apply.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Database Migration & Seeding

Run the following command to create tables in your database:

```bash
php artisan migrate --seed
```

### 6. Serve the Application Locally

You can start the Laravel development server using:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your web browser to access the API.

## Deployment on VPS

### 1. Setting Up a VPS

If you haven't set up a VPS, you can use any popular VPS providers like:

- [DigitalOcean](https://www.digitalocean.com/)
- [Linode](https://www.linode.com/)
- [Vultr](https://www.vultr.com/)

For this guide, we recommend using Ubuntu 20.04 LTS.

### 2. Installing CyberPanel (Recommended)

CyberPanel is an easy-to-use web hosting control panel that simplifies managing websites and applications. Follow these steps to install CyberPanel:

**Install CyberPanel:**

SSH into your server:
```bash
ssh root@your-server-ip
```

Run the installation script:
```bash
sh <(curl -s https://cyberpanel.net/install.sh)
```

Follow the on-screen instructions to complete the installation.

### 3. Deploying Laravel on CyberPanel

1. **Create a Website:**
   - Log in to your CyberPanel admin panel (`http://your-server-ip:8090`) and create a new website.

2. **Upload the Laravel Project:**
   - Clone your GitHub repository or upload your project files via FTP or CyberPanel’s file manager.

3. **Configure the Virtual Host:**
   - Update the `DocumentRoot` to point to the `public` folder of your Laravel installation.

4. **Database Setup:**
   - Create a new database using CyberPanel’s database management feature and update your `.env` file accordingly.

5. **Composer Installation:**
   - SSH into your server, navigate to your Laravel project directory, and run:
     ```bash
     composer install
     ```

6. **Permissions:**
   - Set the correct permissions for storage and cache directories:
     ```bash
     chmod -R 775 storage
     chmod -R 775 bootstrap/cache
     ```

7. **Generate Key & Migrate Database:**
   - Run:
     ```bash
     php artisan key:generate
     php artisan migrate
     ```

8. **Configuring Supervisor for Queue Workers (if using queues):**
   - Install Supervisor:
     ```bash
     apt-get install supervisor
     ```
   - Create a configuration file for Laravel queues:
     ```bash
     nano /etc/supervisor/conf.d/laravel-worker.conf
     ```
     Add the following content:
     ```
     [program:laravel-worker]
     process_name=%(program_name)s_%(process_num)02d
     command=php /path/to/your/laravel/artisan queue:work --sleep=3 --tries=3
     autostart=true
     autorestart=true
     user=your-user
     numprocs=1
     redirect_stderr=true
     stdout_logfile=/path/to/your/laravel/storage/logs/worker.log
     ```
   - Reload Supervisor and start the worker:
     ```bash
     supervisorctl reread
     supervisorctl update
     supervisorctl start laravel-worker:*
     ```
## Frontend 

you can find the frontend here: [Frontend Demo URL](https://fonts.codingzonebd.com/)

## API Documentation

You can find the complete API documentation here: [API Documentation](https://font-backend.codingzonebd.com/api/documentation)

### API Demo URL

You can try the API demo at: [API Demo URL](https://font-backend.codingzonebd.com/api/v1)


## License

This project is licensed under the MIT License.


