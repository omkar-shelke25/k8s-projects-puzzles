# Task: Deploy PHP Website on Kubernetes
The Nautilus DevOps team wants to deploy a PHP website using Apache and MySQL on a Kubernetes cluster. Here are the key steps:

1. **ConfigMap**: Create `php-config` for `php.ini` with `variables_order = "EGPCS"`.
2. **Deployment**: Create `lamp-wp` deployment with two containers:
   - `httpd-php-container` (image: `webdevops/php-apache:alpine-3-php7`), mount `php-config` at `/opt/docker/etc/php/php.ini`.
   - `mysql-container` (image: `mysql:5.6`).
3. **Secrets**: Create a generic secret for MySQL (root password, user, password, host, database) with custom values.
4. **Environment Variables**: Add `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_HOST` to both containers using secret values (via `env` field).
5. **Services**:
   - `lamp-service` (NodePort, port `30008`) for the web app.
   - `mysql-service` (port `3306`) for MySQL.
6. **File Handling**: Copy `/tmp/index.php` from jump_host to `/app` in the httpd container. Replace dummy MySQL values with environment variables (no hardcoding).
7. **Validation**: Access `index.php` on node port `30008` and confirm "Connected successfully" message.


## Task Workflow: Deploy PHP Website on Kubernetes

Follow these steps to complete the deployment of the PHP website:

1. **Create ConfigMap**  
   Define `php-config` with `variables_order = "EGPCS"`.

2. **Create Secrets**  
   Use CLI (`kubectl create secret`) or YAML to set up MySQL credentials (root password, user, password, host, database).

3. **Create Deployment**  
   Define `lamp-wp` deployment with two containers:  
   - `httpd-php-container`: Mount `php-config` at `/opt/docker/etc/php/php.ini`.  
   - `mysql-container`: Set environment variables from Secrets for MySQL.

4. **Create Services**  
   - Set up `lamp-service` as NodePort type on port `30008` to expose the web app.  
   - Set up `mysql-service` on port `3306` for MySQL.

5. **Copy index.php**  
   Copy `/tmp/index.php` from jump_host to the httpd container’s `/app` directory (e.g., via `kubectl cp` or an init container). Ensure MySQL variables in the file use environment variables, not hardcoded values.
