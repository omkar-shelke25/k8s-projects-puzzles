# 🚀 Drupal Application Deployment on Kubernetes Cluster

The Nautilus application development team needs a Kubernetes-based deployment for a fresh Drupal application, installed manually. This README provides the Kubernetes manifests and verification steps to meet the specified requirements.

📅 **Current Date**: March 16, 2025  
👤 **Author**: [Omkar Shelke]

---

## 📋 Problem Statement

The setup must fulfill the following requirements:

1. **Persistent Volume (PV)**:  
   - Name: `drupal-mysql-pv`  
   - `hostPath`: `/drupal-mysql-data` (pre-existing on worker node/jump host)  
   - Capacity: `5Gi`  
   - Access Mode: `ReadWriteOnce`  

2. **Persistent Volume Claim (PVC)**:  
   - Name: `drupal-mysql-pvc`  
   - Storage Request: `3Gi`  
   - Access Mode: `ReadWriteOnce`  

3. **MySQL Deployment**:  
   - Name: `drupal-mysql`  
   - Replicas: `1`  
   - Image: `mysql:5.7`  
   - Mount: `drupal-mysql-pvc` at `/var/lib/mysql`  

4. **Drupal Deployment**:  
   - Name: `drupal`  
   - Replicas: `1`  
   - Image: `drupal:8.6`  

5. **Drupal Service**:  
   - Name: `drupal-service`  
   - Type: `NodePort`  
   - NodePort: `30095`  

6. **MySQL Service**:  
   - Name: `drupal-mysql-service`  
   - Port: `3306`  

7. **Additional Configuration**:  
   - Environment variables and settings for compatibility and functionality.  
   - Access Drupal installation via the "App" button.

---

## 🛠️ Solution: Kubernetes Manifests

Below are the YAML manifests to deploy the Drupal application on Kubernetes.

### 📂 **Persistent Volume (PV)** - `drupal-mysql-pv`
```yaml
apiVersion: v1
kind: PersistentVolume
metadata:
  name: drupal-mysql-pv
spec:
  capacity:
    storage: 5Gi  # 💾 5Gi storage capacity
  volumeMode: Filesystem  # 📁 Filesystem type
  accessModes:
    - ReadWriteOnce  # ✏️ Single node read/write
  persistentVolumeReclaimPolicy: Recycle  # ♻️ Reclaim policy
  storageClassName: manual  # 🛠️ Manual storage class
  hostPath:
    path: /drupal-mysql-data  # 📍 Host path
    type: DirectoryOrCreate  # 🗂️ Creates if not exists
```

### 📜 **Persistent Volume Claim (PVC)** - `drupal-mysql-pvc`
```yaml
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: drupal-mysql-pvc
spec:
  storageClassName: manual  # 🛠️ Matches PV
  accessModes:
    - ReadWriteOnce  # ✏️ Read/write access
  resources:
    requests:
      storage: 3Gi  # 📏 Requests 3Gi
```

### 🗄️ **MySQL Deployment** - `drupal-mysql`
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: drupal-mysql
  labels:
    app: drupal-mysql  # 🏷️ Deployment label
spec:
  replicas: 1  # 🔢 1 replica
  selector:
    matchLabels:
      app: drupal-mysql  # 🔗 Pod selector
  template:
    metadata:
      labels:
        app: drupal-mysql  # 🏷️ Pod label
    spec:
      volumes:
      - name: drupal-mysql-volume  # 💾 Volume name
        persistentVolumeClaim:
          claimName: drupal-mysql-pvc  # 🔗 Links PVC
      containers:
      - name: mysql-container  # 📦 Container name
        image: mysql:5.7  # 🖼️ MySQL 5.7
        ports:
        - containerPort: 3306  # 🔌 MySQL port
        volumeMounts:
        - name: drupal-mysql-volume  # 💾 Mount volume
          mountPath: /var/lib/mysql  # 📍 Mount path
        env:  # 🌍 Environment variables
        - name: MYSQL_ROOT_PASSWORD
          value: "root"  # 🔑 Root password (use a secret in production)
        - name: MYSQL_DATABASE
          value: "drupal"  # 🗄️ Drupal database
        - name: MYSQL_USER
          value: "drupal_user"  # 👤 Database user
        - name: MYSQL_PASSWORD
          value: "omkara25"  # 🔒 User password (use a secret in production)
```

### 🌐 **Drupal Deployment** - `drupal`
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: drupal
  labels:
    app: drupal  # 🏷️ Deployment label
spec:
  replicas: 1  # 🔢 1 replica
  selector:
    matchLabels:
      app: drupal  # 🔗 Pod selector
  template:
    metadata:
      labels:
        app: drupal  # 🏷️ Pod label
    spec:
      containers:
      - name: drupal-container  # 📦 Container name
        image: drupal:8.6  # 🖼️ Drupal 8.6
        ports:
        - containerPort: 80  # 🔌 HTTP port
        env:  # 🌍 Environment variables (optional for Drupal-MySQL connection)
        - name: DRUPAL_DB_HOST
          value: "drupal-mysql-service"  # 🔗 MySQL service name
        - name: DRUPAL_DB_NAME
          value: "drupal"  # 🗄️ Database name
        - name: DRUPAL_DB_USER
          value: "drupal_user"  # 👤 Database user
        - name: DRUPAL_DB_PASSWORD
          value: "omkara25"  # 🔒 Database password
```

### 🔗 **Drupal Service** - `drupal-service`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: drupal-service  # 📛 Service name
spec:
  selector:
    app: drupal  # 🔗 Targets Drupal pods
  type: NodePort  # 🌍 External access
  ports:
  - name: drupal-port  # 📌 Port name
    protocol: TCP  # 📡 TCP protocol
    port: 80  # 🔌 Service port
    targetPort: 80  # 🎯 Container port
    nodePort: 30095  # 🌐 External NodePort
```

### 🔗 **MySQL Service** - `drupal-mysql-service`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: drupal-mysql-service  # 📛 Service name
spec:
  selector:
    app: drupal-mysql  # 🔗 Targets MySQL pods
  type: ClusterIP  # 🔒 Internal service
  ports:
  - name: mysql-port  # 📌 Port name
    protocol: TCP  # 📡 TCP protocol
    port: 3306  # 🔌 Service port
    targetPort: 3306  # 🎯 Container port
```

---

## ✅ Verification Steps

Run these `kubectl` commands on the jump host to confirm the setup:

```bash
kubectl get pv        # 📂 Check Persistent Volume
kubectl get pvc       # 📜 Check Persistent Volume Claim
kubectl get deploy    # 🚀 Check Deployments
kubectl get po        # 📦 Check Pods
kubectl get svc       # 🌐 Check Services
kubectl logs <mysql-pod-name>  # 📜 Check MySQL logs (e.g., drupal-mysql-54d7b7cf85-xvqx2)
```

### Example Output Screenshots
📸 *Add your screenshots here*  
- Replace placeholders with actual images from your environment:
  - ![PV and PVC Status](https://github.com/user-attachments/assets/44476344-c3c1-40cf-bb9d-494ddfc64b8d)
  - ![Deployments and Services](https://github.com/user-attachments/assets/860f283d-922f-4953-8d3d-2f6554bc1c4e)

---

## ⚙️ Deployment Instructions

1. **Apply Manifests**:
   ```bash
   kubectl apply -f pv.yaml
   kubectl apply -f pvc.yaml
   kubectl apply -f mysql-deployment.yaml
   kubectl apply -f drupal-deployment.yaml
   kubectl apply -f drupal-service.yaml
   kubectl apply -f mysql-service.yaml
   ```

2. **Access Drupal**:
   - Open `http://<node-ip>:30095` in a browser.
   - Complete the Drupal installation using the MySQL credentials provided.

3. **Troubleshooting**:
   - Check pod logs: `kubectl logs <pod-name>`
   - Describe resources: `kubectl describe <resource> <name>`

---

## 🔒 Security Notes
- Replace hardcoded passwords (`MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD`, etc.) with Kubernetes Secrets in a production environment.
- Example Secret:
  ```yaml
  apiVersion: v1
  kind: Secret
  metadata:
    name: mysql-secrets
  type: Opaque
  data:
    root-password: cm9vdA==  # base64 for "root"
    user-password: b21rYXJhMjU=  # base64 for "omkara25"
  ```

---

