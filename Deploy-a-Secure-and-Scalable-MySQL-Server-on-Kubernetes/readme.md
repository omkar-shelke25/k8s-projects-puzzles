# 🚀 **Problem Statement: Deploy a Secure and Scalable MySQL Server on Kubernetes**  

The Nautilus DevOps team has recently finalized the requirements for deploying a secure and scalable MySQL server on a Kubernetes cluster. Your task is to set up the entire MySQL infrastructure from scratch, including storage, deployment, service, and secure credentials management.  

The goal is to create a robust MySQL deployment that can handle production-level workloads while ensuring secure access and persistence of data. You will need to create persistent storage to retain MySQL data across pod restarts, set up a secure NodePort service to expose MySQL for external access, and configure sensitive credentials using Kubernetes secrets.  

Additionally, the deployment should be configured with environment variables to securely inject database credentials at runtime. Properly mounting persistent volumes and using secrets will ensure data integrity and security, which are critical for production-grade deployments.  

You are required to use `kubectl` to create the necessary Kubernetes objects, configure them correctly, and validate that the MySQL server is up and running with the correct settings. All configurations should follow Kubernetes best practices to ensure scalability, reliability, and security.  

This deployment will enable the Nautilus team to efficiently manage MySQL data, improve access control, and handle database scaling without downtime.  

---

## 📝 **Detailed Requirements:**  

### 1. 📦 **Persistent Volume Configuration**  
You need to create a Persistent Volume to ensure that MySQL data persists across pod restarts.  
✅ Create a **PersistentVolume** named `mysql-pv`  
✅ Set the capacity to **250Mi**  
✅ Define other parameters such as access modes and storage class based on Kubernetes best practices  

---

### 2. 🎯 **Persistent Volume Claim**  
To request storage from the Persistent Volume, you must create a Persistent Volume Claim.  
✅ Create a **PersistentVolumeClaim** named `mysql-pv-claim`  
✅ Request **250Mi** of storage  
✅ Ensure the claim is bound to the Persistent Volume  

---

### 3. 🏗️ **MySQL Deployment**  
Deploy a MySQL server using a Kubernetes Deployment.  
✅ Create a **Deployment** named `mysql-deployment`  
✅ Use a stable and secure MySQL image (e.g., `mysql:latest`)  
✅ Mount the Persistent Volume at `/var/lib/mysql` to ensure data persistence  
✅ Ensure the deployment is configured with the necessary environment variables for secure access  

---

### 4. 🌐 **Service Configuration**  
Create a NodePort service to expose the MySQL server for external access.  
✅ Create a **NodePort** type service named `mysql`  
✅ Set the `nodePort` to **30007**  
✅ Ensure the service allows secure and restricted access  

---

### 5. 🔒 **Secret Management**  
To secure MySQL credentials, you need to create Kubernetes Secrets.  
✅ **mysql-root-pass** → Store root password securely  
- `key = password` → `value = YUIidhb667`  

✅ **mysql-user-pass** → Store MySQL user credentials  
- `key = username` → `value = kodekloud_cap`  
- `key = password` → `value = dCV3szSGNA`  

✅ **mysql-db-url** → Store database name  
- `key = database` → `value = kodekloud_db9`  

---

### 6. 🌍 **Environment Variable Configuration**  
The MySQL container should read credentials securely from the Kubernetes Secrets using environment variables.  

| **Environment Variable**   | **Source**                  | **Key**           |  
|----------------------------|-----------------------------|-------------------|  
| `MYSQL_ROOT_PASSWORD`       | `secretKeyRef: mysql-root-pass` | `password`         |  
| `MYSQL_DATABASE`            | `secretKeyRef: mysql-db-url`     | `database`         |  
| `MYSQL_USER`                | `secretKeyRef: mysql-user-pass`   | `username`         |  
| `MYSQL_PASSWORD`            | `secretKeyRef: mysql-user-pass`   | `password`         |  






![image](https://github.com/user-attachments/assets/e2c10be1-1656-4dfc-8eb0-a4c568177661)


