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

## Implmentation
Here’s a refined version with better formatting and clarity:  

### **Step 1: Define a ConfigMap for PHP Configuration**  
Create a `ConfigMaps.yaml` file and define the `php-config` ConfigMap:  

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  # ConfigMap name
  name: php-config
data:
  # PHP configuration file
  php.ini: |
    # Set variable order for PHP
    variables_order = "EGPCS"
```

Apply the ConfigMap using:  
```sh
kubectl apply -f ConfigMaps.yaml
```

### **Step 2: Create Kubernetes Secrets via CLI**  
Use the following command to create a Secret named `mysql-secrets1` containing MySQL credentials:  

```sh
kubectl create secret generic mysql-secrets \
  --from-literal=MYSQL_ROOT_PASSWORD=root \
  --from-literal=MYSQL_DATABASE=lamp_db \
  --from-literal=MYSQL_USER=omkar \
  --from-literal=MYSQL_PASSWORD=pass \
  --from-literal=MYSQL_HOST=mysql-service \
  -o yaml > mysql-secrets.yaml
```

This command generates a YAML file (`mysql-secrets.yaml`) with the secret configuration, which can be applied later using:  

```sh
kubectl apply -f mysql-secrets.yaml
```
### step 3: **Deployment:** Create `lamp-wp` deployment with two containers:  
- **httpd-php-container** (image: `webdevops/php-apache:alpine-3-php7`), mount `php-config` at `/opt/docker/etc/php/php.ini`.  
- **mysql-container** (image: `mysql:5.6`).  

After creating the deployment file **lamp-wp-deployment.yaml**, execute the following command to apply it:  
```sh
kubectl apply -f lamp-wp-deployment.yaml
```
Both containers should now be running. 🚀

### **Steps 4: Access `index.php` File in the `httpd` Container**  

1. **Create Services:**  
   - `lamp-service`: Exposes the `httpd-php-container` on **NodePort 30008** for external access.  
   - `mysql-service`: Exposes the `mysql-container` internally on **port 3306** for database connectivity.  

2. **Apply the Service Configurations:**  
   ```sh
   kubectl apply -f lamp-service.yaml
   kubectl apply -f mysql-service.yaml
   ```

3. **Copy `index.php` to the HTTPD Container:**  
   ```sh
   kubectl cp /tmp/index.php lamp-wp-644b44999-lfr92:/app -c httpd-php-container
   ```

4. **Access `index.php` in a Browser:**  
   - Find the node’s IP using:  
     ```sh
     kubectl get nodes -o wide
     ```
   - Open the browser and navigate to:  
     ```
     http://<Node-IP>:30008/index.php
     ```
     Now, your `index.php` should be accessible! 🚀
    ![image](https://github.com/user-attachments/assets/ea6d6f2f-408a-4c14-9d52-ec957a1dedaa)


### **Step 5: Verify All Running Resources**  

Run the following command to check the status of all Kubernetes resources:  

```sh
kubectl get svc,po,configmaps,secrets
```

This will display the status of:  
- **Services (`svc`)** – To confirm that `lamp-service` and `mysql-service` are running.  
- **Pods (`po`)** – To ensure `lamp-wp` pods are running successfully.  
- **ConfigMaps (`configmaps`)** – To verify `php-config` is correctly mounted.  
- **Secrets (`secrets`)** – If any database credentials are stored as secrets.  

If all resources are running correctly, your LAMP deployment is successfully set up! 🚀
![image](https://github.com/user-attachments/assets/0fef6267-eccb-4f1e-b295-c299886bb343)


