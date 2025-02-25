

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

