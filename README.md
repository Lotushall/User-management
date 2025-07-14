# Ci4 Docker Skeleton Template

A ready-to-use **template repository** for developing applications with CodeIgniter 4, fully integrated with Docker for a simple and flexible workflow.

---

## Features

- Template repository: Click "Use this template" on GitHub to start your own project instantly
- Dockerized PHP 8.1 + Apache environment
- Pre-installed PHP extensions for CodeIgniter 4
- MySQL 8 database container with persistent volume
- phpMyAdmin for easy DB management
- Composer installed and auto-run inside the container
- Apache mod_rewrite enabled for clean URLs
- Volume mounting for live code editing
- Environment variable support via .env

---

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Usage

1. Click **"Use this template"** on GitHub to create your own repository
2. Clone your new repository

   ```bash
   git clone https://github.com/your-username/your-repo.git
   cd your-repo
   ```

3. Copy `.env.example` to `.env` and update environment variables as needed
4. Build and start the Docker containers:

   ```bash
   docker-compose up -d --build
   ```

5. Wait a moment for Composer autoload and dependencies to finish installing inside the container (first run may take a while)
6. Access your app at http://localhost:8080 (or your configured port)
7. Access phpMyAdmin at http://localhost:8081 (or your configured port)

## Configuration
- PHP config: `./docker/php/php.ini`
- MySQL config: `./docker/mysql/my.cnf`
- App environment: `.env`

## Folder Structure

```bash
Ci4_docker_skeleton/
├── app/                 # CodeIgniter 4 application code
├── docker/
│   ├── apache/          # Apache config
│   ├── mysql/           # MySQL config
│   └── php/             # PHP config
├── writable/            # Logs, cache, sessions
├── docker-compose.yml   # Docker Compose config
├── Dockerfile           # PHP/Apache Dockerfile
├── composer.json        # Composer dependencies
└── .env.example         # Example environment variables
```

## Troubleshooting
- Permission issues: Ensure `writable/` has correct permissions
- If the app is not available right after `docker-compose up`, wait a bit longer for Composer to finish installing dependencies
- Rebuild containers without cache:
  ```bash
  docker-compose build --no-cache
  docker-compose up -d
  ```
- Composer errors: Check required PHP extensions in Dockerfile

## License
MIT License. See the LICENSE file for details.
