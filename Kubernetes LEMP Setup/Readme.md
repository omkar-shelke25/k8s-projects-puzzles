

# 🚀 **Deploy a Static Website on Kubernetes Cluster**  

The Nautilus DevOps team wants to deploy a static website on a **Kubernetes cluster** using **Nginx**, **PHP-FPM**, and **MySQL**. Follow the instructions below to make the website live.

---

## 🛠 **Task Details**  

### 🔐 **Step 1: Create Secrets for MySQL**  
Create the following **secrets** in Kubernetes:

#### 📌 **Secret: `mysql-root-pass`**  
- **Key:** `password`  
- **Value:** `R00t`  

#### 📌 **Secret: `mysql-user-pass`**  
- **Key:** `username` → `kodekloud_gem`  
- **Key:** `password` → `GyQkFRVNr3`  

#### 📌 **Secret: `mysql-db-url`**  
- **Key:** `database` → `kodekloud_db10`  

#### 📌 **Secret: `mysql-host`**  
- **Key:** `host` → `mysql-service`  

---

### ⚙️ **Step 2: Create ConfigMap for PHP**  
Create a **ConfigMap** named `php-config` with the following data:

- **Key:** `php.ini`  
- **Value:** `variables_order = "EGPCS"`

---

### 🏗 **Step 3: Create Deployment (`lemp-wp`)**  
- Create a **Deployment** named `lemp-wp`  
- Define **two containers**:  
  - `nginx-php-container` (using image `webdevops/php-nginx:alpine-3-php7`)  
  - `mysql-container` (using image `mysql:5.6`)  
- **Mount `php-config` ConfigMap** in the **Nginx container** at:  
  - `/opt/docker/etc/php/php.ini`  

#### 🏷 **Environment Variables (Both Containers)**  
Set the following **environment variables** (Do **not** use `envFrom`):

| **Variable**          | **Secret Name**      | **Key**        |
|----------------------|--------------------|--------------|
| `MYSQL_ROOT_PASSWORD` | `mysql-root-pass`  | `password`   |
| `MYSQL_DATABASE`      | `mysql-db-url`     | `database`   |
| `MYSQL_USER`         | `mysql-user-pass`  | `username`   |
| `MYSQL_PASSWORD`     | `mysql-user-pass`  | `password`   |
| `MYSQL_HOST`        | `mysql-host`       | `host`       |

---

### 🌐 **Step 4: Create Services**  

#### 🌍 **Service 1: `lemp-service` (NodePort for Web App)**  
- **Type:** `NodePort`  
- **Port:** `80`  
- **TargetPort:** `80`  
- **NodePort:** `30008`  

#### 🛢 **Service 2: `mysql-service`**  
- **Type:** `ClusterIP`  
- **Port:** `3306`  

---

### 📂 **Step 5: Copy `index.php` and Configure MySQL Connection**  
A **`/tmp/index.php`** file is present on the **jump_host server**.  

✅ **Copy this file into the Nginx container under `/app`**  

🚫 **Do not hardcode MySQL details**.  
❗ Replace MySQL connection variables with **environment variables**.

---

### ✅ **Step 6: Verify Deployment**  
Access the **website** using the **Website Button** in the **top bar**.  

💡 You should see **"Connected successfully"** on the page.  

---

## 📋 Steps and Manifests

### 1️⃣ Create Secrets for MySQL
We'll create four secrets to store MySQL credentials and configuration securely.

#### Secret 1: `mysql-root-pass`
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysql-root-pass
type: Opaque
data:
  password: UjAwdA== # Base64 encoded value of "R00t"
```

#### Secret 2: `mysql-user-pass`
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysql-user-pass
type: Opaque
data:
  username: a29kZWtsb3VkX2dlbQ== # Base64 encoded value of "kodekloud_gem"
  password: R3lRa0ZSVk5yMw==   # Base64 encoded value of "GyQkFRVNr3"
```

#### Secret 3: `mysql-db-url`
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysql-db-url
type: Opaque
data:
  database: a29kZWtsb3VkX2RiMTA= # Base64 encoded value of "kodekloud_db10"
```

#### Secret 4: `mysql-host`
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysql-host
type: Opaque
data:
  host: bXlzcWwtc2VydmljZQ== # Base64 encoded value of "mysql-service"
```

---

### 2️⃣ Create ConfigMap for PHP Configuration
Create a ConfigMap named `php-config` for the `php.ini` file.

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: php-config
data:
  php.ini: |
    variables_order = "EGPCS"
```

---

### 3️⃣ Create Deployment `lemp-wp`
Define a deployment with two containers: Nginx+PHP-FPM and MySQL.

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: lemp-wp
spec:
  replicas: 1
  selector:
    matchLabels:
      app: lemp-wp
  template:
    metadata:
      labels:
        app: lemp-wp
    spec:
      containers:
      - name: nginx-php-container
        image: webdevops/php-nginx:alpine-3-php7
        env:
        - name: MYSQL_ROOT_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-root-pass
              key: password
        - name: MYSQL_DATABASE
          valueFrom:
            secretKeyRef:
              name: mysql-db-url
              key: database
        - name: MYSQL_USER
          valueFrom:
            secretKeyRef:
              name: mysql-user-pass
              key: username
        - name: MYSQL_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-user-pass
              key: password
        - name: MYSQL_HOST
          valueFrom:
            secretKeyRef:
              name: mysql-host
              key: host
        volumeMounts:
        - name: php-config-volume
          mountPath: /opt/docker/etc/php/php.ini
          subPath: php.ini
      - name: mysql-container
        image: mysql:5.6
        env:
        - name: MYSQL_ROOT_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-root-pass
              key: password
        - name: MYSQL_DATABASE
          valueFrom:
            secretKeyRef:
              name: mysql-db-url
              key: database
        - name: MYSQL_USER
          valueFrom:
            secretKeyRef:
              name: mysql-user-pass
              key: username
        - name: MYSQL_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-user-pass
              key: password
        - name: MYSQL_HOST
          valueFrom:
            secretKeyRef:
              name: mysql-host
              key: host
      volumes:
      - name: php-config-volume
        configMap:
          name: php-config
```

---

### 4️⃣ Create NodePort Service `lemp-service`
Expose the web application on port `30008`.

```yaml
apiVersion: v1
kind: Service
metadata:
  name: lemp-service
spec:
  type: NodePort
  ports:
  - port: 80
    targetPort: 80
    nodePort: 30008
  selector:
    app: lemp-wp
```

---

### 5️⃣ Create MySQL Service `mysql-service`
Expose MySQL on port `3306`.

```yaml
apiVersion: v1
kind: Service
metadata:
  name: mysql-service
spec:
  ports:
  - port: 3306
    targetPort: 3306
  selector:
    app: lemp-wp
```

---

### 6️⃣ Update and Copy `index.php` & Verify Deployment
Copy the `/tmp/index.php` file from the `jump_host` to the Nginx container's document root (`/app`) and ensure it uses environment variables for MySQL connection details.

![image](https://github.com/user-attachments/assets/3e01cbf3-e711-4024-b488-eaaced8fd3c9)

![image](https://github.com/user-attachments/assets/72e741ac-5612-4570-9101-5b9319a233f0)


