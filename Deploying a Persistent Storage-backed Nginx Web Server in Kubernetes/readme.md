# Kubernetes Deployment Template for Web Application

## Task Statement  
The Nautilus DevOps team is tasked with deploying a web application on a Kubernetes cluster. The deployment requires the creation and configuration of Persistent Volumes (PV) and Persistent Volume Claims (PVC) to store the application code. Below are the detailed tasks to achieve this deployment.

---

## Task Details  

### **1. Create a PersistentVolume (PV)**  
Create a PersistentVolume named `pv-devops` with the following specifications:  
- **Storage Class**: `manual`  
- **Capacity**: `4Gi`  
- **Access Mode**: `ReadWriteOnce`  
- **Volume Type**: `hostPath`  
- **Path**: `/mnt/itadmin` (Note: This directory is pre-created and does not require direct access.)  

---

### **2. Create a PersistentVolumeClaim (PVC)**  
Create a PersistentVolumeClaim named `pvc-devops` with the following specifications:  
- **Storage Class**: `manual`  
- **Storage Request**: `1Gi`  
- **Access Mode**: `ReadWriteOnce`  

---

### **3. Create a Pod**  
Create a Pod named `pod-devops` with the following specifications:  
- **Container Name**: `container-devops`  
- **Image**: `nginx:latest` (Ensure the `latest` tag is explicitly mentioned.)  
- **Volume Mount**: Mount the PersistentVolumeClaim `pvc-devops` at the document root of the web server.  

---

### **4. Create a Service**  
Create a NodePort service named `web-devops` with the following specifications:  
- **Type**: `NodePort`  
- **Node Port**: `30008`  
- **Purpose**: Expose the web server running within the `pod-devops`.  

---


## 🛠️ Implementation Steps

### 1. **Create PersistentVolume (PV)**
Create a PersistentVolume named `pv-devops` to provide storage for the application.

#### Command:
```bash
kubectl apply -f pv-devops.yaml
```

#### YAML File: `pv-devops.yaml`
```yaml
apiVersion: v1
kind: PersistentVolume
metadata:
  name: pv-devops
spec:
  capacity:
    storage: 4Gi
  volumeMode: Filesystem
  accessModes:
    - ReadWriteOnce
  persistentVolumeReclaimPolicy: Recycle
  storageClassName: manual
  hostPath:
    path: /mnt/itadmin
```

---

### 2. **Create PersistentVolumeClaim (PVC)**
Create a PersistentVolumeClaim named `pvc-devops` to request storage from the PersistentVolume.

#### Command:
```bash
kubectl apply -f pvc-devops.yaml
```

#### YAML File: `pvc-devops.yaml`
```yaml
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: pvc-devops
spec:
  accessModes:
    - ReadWriteOnce
  volumeMode: Filesystem
  resources:
    requests:
      storage: 1Gi
  storageClassName: manual
```

---

### 3. **Create Pod**
Create a Pod named `pod-devops` that uses the PersistentVolumeClaim to store application data. The Nginx container serves files from the mounted volume.

#### Command:
```bash
kubectl apply -f pod-devops.yaml
```

#### YAML File: `pod-devops.yaml`
```yaml
apiVersion: v1
kind: Pod
metadata:
  name: pod-devops
  labels:
    app: nginx
spec:
  volumes:
  - name: pod-volume
    persistentVolumeClaim:
      claimName: pvc-devops
  containers:
  - name: container-devops
    image: nginx:latest
    ports:
    - containerPort: 80
    volumeMounts: 
    - name: pod-volume
      mountPath: "/usr/share/nginx/html"
```

---

### 4. **Create Service**
Create a NodePort service named `web-service` to expose the Nginx web server.

#### Command:
```bash
kubectl apply -f web-service.yaml
```

#### YAML File: `web-service.yaml`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: web-service
spec:
  selector:
    app: nginx
  type: NodePort
  ports:
    - protocol: TCP
      port: 80
      targetPort: 80 
      nodePort: 30008
```

---

## 🖥️ Final Outputs

### Verify Resources
1. **Check PersistentVolume (PV):**
   ```bash
   kubectl get pv pv-devops
   ```

2. **Check PersistentVolumeClaim (PVC):**
   ```bash
   kubectl get pvc pvc-devops
   ```

3. **Check Pod:**
   ```bash
   kubectl get pod pod-devops
   ```

4. **Check Service:**
   ```bash
   kubectl get service web-service
   ```

---

## 📂 Directory Overwrite in Container
When a PersistentVolumeClaim (PVC) is mounted to a directory in a container (e.g., `/usr/share/nginx/html`), the contents of the PVC **override** the existing directory in the container. Here’s what happens:

- **Default Nginx Directory**: By default, `/usr/share/nginx/html` contains an `index.html` file, which serves the Nginx welcome page.
- **Mounting PVC**: When the PVC is mounted, the default content is replaced by the files in the PVC. If the PVC is empty, the directory will appear empty in the container.
- **Behavior**: The container will only serve files from the PVC, and the original files in `/usr/share/nginx/html` will no longer be accessible.

---

## 🚀 Access the Application
Once the deployment is complete, access the web application using the NodePort service:

- **URL**: `http://<Node-IP>:30008`
- Replace `<Node-IP>` with the IP address of any node in your Kubernetes cluster.

---

## 🎯 Summary
This deployment ensures that the web application's data is stored persistently using Kubernetes PersistentVolume and PersistentVolumeClaim. The application is exposed via a NodePort service, making it accessible externally. The mounted PVC overwrites the default Nginx directory, ensuring that the application serves custom content. 

---

