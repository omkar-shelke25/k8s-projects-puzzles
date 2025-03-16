
**Problem Statement: Deploying a Drupal Application on Kubernetes Cluster**

The Nautilus application development team requires a Kubernetes-based deployment for a fresh Drupal application, which they will install manually. The setup must meet the following specifications:

1. **Persistent Volume Configuration**:  
   Create a Persistent Volume named `drupal-mysql-pv` with:  
   - `hostPath` set to `/drupal-mysql-data` (pre-existing directory on the worker node/jump host).  
   - Storage capacity of 5Gi.  
   - Access mode set to `ReadWriteOnce`.

2. **Persistent Volume Claim Configuration**:  
   Create a PersistentVolumeClaim named `drupal-mysql-pvc` with:  
   - Storage request of 3Gi.  
   - Access mode set to `ReadWriteOnce`.

3. **MySQL Deployment**:  
   Deploy a Kubernetes Deployment named `drupal-mysql` with:  
   - 1 replica.  
   - Image: `mysql:5.7`.  
   - Mount the `drupal-mysql-pvc` at `/var/lib/mysql`.

4. **Drupal Deployment**:  
   Deploy a Kubernetes Deployment named `drupal` with:  
   - 1 replica.  
   - Image: `drupal:8.6`.

5. **Drupal Service**:  
   Create a `NodePort` Service named `drupal-service` with:  
   - NodePort set to `30095`.  
   - Exposing the Drupal deployment for external access.

6. **MySQL Service**:  
   Create a Service named `drupal-mysql-service` to:  
   - Expose the `drupal-mysql` deployment on port `3306`.

7. **Additional Configuration**:  
   Configure any necessary settings (e.g., environment variables, secrets) for the deployments and services to ensure compatibility and functionality. The final setup should allow access to the Drupal installation page via the "App" button.

To verify that the Kubernetes resources for the Drupal application deployment are correctly created and functioning, you can use a series of `kubectl` commands on the jump host. Below, I’ve updated the solution with verification steps added after the manifests. These steps ensure that each resource (PersistentVolume, PersistentVolumeClaim, Deployments, and Services) is properly configured and running.

---

## Solution: Kubernetes Manifests for Drupal Application Deployment with Verification

Below are the Kubernetes configuration files to deploy the Drupal application, followed by commands to verify the resources.

### 📂 Persistent Volume (PV) - `drupal-mysql-pv`
```yaml
apiVersion: v1
kind: PersistentVolume
metadata:
  name: drupal-mysql-pv
spec:
  capacity:
    storage: 5Gi  # 🔍 Defines 5Gi of storage capacity
  volumeMode: Filesystem  # 📁 Specifies the volume as a filesystem
  accessModes:
    - ReadWriteOnce  # ✏️ Allows read/write access by a single node
  persistentVolumeReclaimPolicy: Recycle  # ♻️ Reclaims space when PVC is deleted
  storageClassName: manual  # 🛠️ Custom storage class for manual management
  hostPath:
    path: /drupal-mysql-data  # 📍 Path on the worker node (jump host)
    type: DirectoryOrCreate  # 🗂️ Creates the directory if it doesn’t exist
```

### 📜 Persistent Volume Claim (PVC) - `drupal-mysql-pvc`
```yaml
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: drupal-mysql-pvc
spec:
  storageClassName: manual  # 🛠️ Matches the PV’s storage class
  accessModes:
    - ReadWriteOnce  # ✏️ Matches PV’s access mode
  resources:
    requests:
      storage: 3Gi  # 📏 Requests 3Gi of storage from the PV
```

### 🚀 Deployment - `drupal-mysql`
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: drupal-mysql
  labels:
    app: drupal-mysql  # 🏷️ Labels the deployment
spec:
  replicas: 1  # 🔢 Ensures 1 instance of MySQL runs
  selector:
    matchLabels:
      app: drupal-mysql  # 🔗 Matches pods with this label
  template:
    metadata:
      labels:
        app: drupal-mysql  # 🏷️ Labels for the pod
    spec:
      volumes:
      - name: drupal-mysql-volume  # 💾 Volume name for MySQL data
        persistentVolumeClaim:
          claimName: drupal-mysql-pvc  # 🔗 Links to the PVC
      containers:
      - name: mysql-container  # 📦 Container name
        image: mysql:5.7  # 🖼️ MySQL image version
        ports:
        - containerPort: 3306  # 🔌 Exposes MySQL port
        volumeMounts:
        - name: drupal-mysql-volume  # 💾 Mounts the volume
          mountPath: /var/lib/mysql  # 📍 Mount point for MySQL data
        env:  # 🌍 Environment variables for MySQL configuration
        - name: MYSQL_ROOT_PASSWORD
          value: "root"  # 🔑 Root password (replace with secure value)
        - name: MYSQL_DATABASE
          value: "drupal"  # 🗄️ Database name for Drupal
        - name: MYSQL_USER
          value: "drupal_user"  # 👤 Database user
        - name: MYSQL_PASSWORD
          value: "omkara25"  # 🔒 User password (replace with secure value)
```

### 🚀 Deployment - `drupal`
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: drupal
  labels:
    app: drupal  # 🏷️ Labels the deployment
spec:
  replicas: 1  # 🔢 Ensures 1 instance of Drupal runs
  selector:
    matchLabels:
      app: drupal  # 🔗 Matches pods with this label
  template:
    metadata:
      labels:
        app: drupal  # 🏷️ Labels for the pod
    spec:
      containers:
      - name: drupal-container  # 📦 Container name
        image: drupal:8.6  # 🖼️ Drupal image version
        ports:
        - containerPort: 80  # 🔌 Exposes HTTP port for Drupal
```

### 🌐 Service - `drupal-service`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: drupal-service  # 📛 Service name
spec:
  selector:
    app: drupal  # 🔗 Targets Drupal deployment pods
  type: NodePort  # 🌍 Exposes service externally via NodePort
  ports:
  - name: name-of-drupal-service-port  # 📌 Port name
    protocol: TCP  # 📡 Protocol type
    port: 80  # 🔌 Service port
    targetPort: 80  # 🎯 Container port
    nodePort: 30095  # 🌐 External port accessible on the node
```

### 🌐 Service - `drupal-mysql-service`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: drupal-mysql-service  # 📛 Service name
spec:
  selector:
    app: drupal-mysql  # 🔗 Targets MySQL deployment pods
  type: ClusterIP  # 🔒 Internal service (default type)
  ports:
  - name: name-of-drupal-mysql-service-port  # 📌 Port name
    protocol: TCP  # 📡 Protocol type
    port: 3306  # 🔌 Service port
    targetPort: 3306  # 🎯 Container port
```

---

### Verification Steps

```bash
k get pv
k get pvc
k get deploy
k get po
k get svc
k logs drupal-mysql-54d7b7cf85-xvqx2
```

![image](https://github.com/user-attachments/assets/44476344-c3c1-40cf-bb9d-494ddfc64b8d)

![image](https://github.com/user-attachments/assets/860f283d-922f-4953-8d3d-2f6554bc1c4e)


---





