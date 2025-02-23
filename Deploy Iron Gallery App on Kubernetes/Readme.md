## 🛠 Iron Gallery Deployment Task

The Nautilus DevOps team has recently customized the Iron Gallery app and is preparing to deploy it on a Kubernetes cluster. Below are the detailed requirements for the deployment:

---
### 🔢 Namespace Creation
- Create a namespace: `iron-namespace-nautilus`

---
### 🛠 Iron Gallery Deployment
- Create a deployment: `iron-gallery-deployment-nautilus` under the namespace `iron-namespace-nautilus`.
- **Labels**: `run` should be `iron-gallery`.
- **Replicas**: 1
- **Selector**: `matchLabels` `run` should be `iron-gallery`.
- **Template Labels**: `run` should be `iron-gallery` under metadata.
- **Container**:
  - Name: `iron-gallery-container-nautilus`
  - Image: `kodekloud/irongallery:2.0` (use exact image name/tag).
- **Resource Limits**:
  - Memory: `100Mi`
  - CPU: `50m`
- **Volume Mounts**:
  1. Name: `config`, Mount Path: `/usr/share/nginx/html/data`
  2. Name: `images`, Mount Path: `/usr/share/nginx/html/uploads`
- **Volumes**:
  1. Name: `config`, Type: `emptyDir`
  2. Name: `images`, Type: `emptyDir`

---
### 🏛 Iron DB Deployment
- Create a deployment: `iron-db-deployment-nautilus` under the namespace `iron-namespace-nautilus`.
- **Labels**: `db` should be `mariadb`.
- **Replicas**: 1
- **Selector**: `matchLabels` `db` should be `mariadb`.
- **Template Labels**: `db` should be `mariadb` under metadata.
- **Container**:
  - Name: `iron-db-container-nautilus`
  - Image: `kodekloud/irondb:2.0` (use exact image name/tag).
- **Environment Variables**:
  - `MYSQL_DATABASE`: `database_blog`
  - `MYSQL_ROOT_PASSWORD`: `<complex password>`
  - `MYSQL_PASSWORD`: `<complex password>`
  - `MYSQL_USER`: `<custom user>` (not `root`)
- **Volume Mounts**:
  - Name: `db`, Mount Path: `/var/lib/mysql`
- **Volumes**:
  - Name: `db`, Type: `emptyDir`

---
### 🛡 Iron DB Service
- Create a service: `iron-db-service-nautilus` under the namespace `iron-namespace-nautilus`.
- **Selector**: `db` should be `mariadb`.
- **Spec**:
  - Protocol: `TCP`
  - Port: `3306`
  - TargetPort: `3306`
  - Type: `ClusterIP`

---
### 🏡 Iron Gallery Service
- Create a service: `iron-gallery-service-nautilus` under the namespace `iron-namespace-nautilus`.
- **Selector**: `run` should be `iron-gallery`.
- **Spec**:
  - Protocol: `TCP`
  - Port: `80`
  - TargetPort: `80`
  - NodePort: `32678`
  - Type: `NodePort`
  - 
# Implementation: Iron Nautilus Kubernetes Deployment
1. **Create the Kubernetes Namespace**
   ```sh
   kubectl create namespace iron-namespace-nautilus
   ```
2. **Apply the Kubernetes configurations** (see below for details).

## Components
### Namespace
All resources are deployed under `iron-namespace-nautilus`.

### Secrets
The **mysql-secrets** resource stores MySQL credentials securely.
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysql-secrets
  namespace: iron-namespace-nautilus
type: Opaque
data:
  MYSQL_DATABASE: ZGF0YWJhc2VfYmxvZw==
  MYSQL_HOST: aXJvbi1kYi1zZXJ2aWNlLW5hdXRpbHVz
  MYSQL_PASSWORD: cGFzcw==
  MYSQL_ROOT_PASSWORD: cm9vdA==
  MYSQL_USER: b21rYXI=
```

### Deployments
#### **Iron DB Deployment** (MariaDB)
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: iron-db-deployment-nautilus
  namespace: iron-namespace-nautilus
  labels:
    db: mariadb
spec:
  replicas: 1
  selector:
    matchLabels:
      db: mariadb
  template:
    metadata:
      labels:
        db: mariadb
    spec:
      volumes:
      - name: db
        emptyDir: {}
      containers:
      - name: iron-db-container-nautilus
        image: kodekloud/irondb:2.0
        volumeMounts:
        - name: db
          mountPath: "/var/lib/mysql"
        ports:
          - containerPort: 3306
        envFrom:
          - secretRef:
              name: mysql-secrets
```

#### **Iron Gallery Deployment**
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: iron-gallery-deployment-nautilus
  namespace: iron-namespace-nautilus
  labels:
    run: iron-gallery
spec:
  replicas: 1
  selector:
    matchLabels:
      run: iron-gallery
  template:
    metadata:
      labels:
        run: iron-gallery
    spec:
      volumes:
      - name: config
        emptyDir: {}
      - name: images
        emptyDir: {}
      containers:
      - name: iron-gallery-container-nautilus
        image: kodekloud/irongallery:2.0
        resources:
          limits:
            cpu: "50m"
            memory: "100Mi"
        ports:
        - containerPort: 80
        volumeMounts:
        - name: config
          mountPath: /usr/share/nginx/html/data
        - name: images
          mountPath: /usr/share/nginx/html/uploads
```

### Services
#### **Iron DB Service (ClusterIP)**
```yaml
apiVersion: v1
kind: Service
metadata:
  name: iron-db-service-nautilus
  namespace: iron-namespace-nautilus
spec:
  selector:
    db: mariadb
  ports:
    - protocol: TCP
      port: 3306
      targetPort: 3306
  type: ClusterIP
```

#### **Iron Gallery Service (NodePort)**
```yaml
apiVersion: v1
kind: Service
metadata:
  name: iron-gallery-service-nautilus
  namespace: iron-namespace-nautilus
spec:
  selector:
    run: iron-gallery
  ports:
    - protocol: TCP
      port: 80
      targetPort: 80
      nodePort: 32678
  type: NodePort
```

## Applying Configurations
Run the following command to apply all configurations:
```sh
kubectl apply -f .
```

## Verifying Deployment
1. **Check the running pods**
   ```sh
   kubectl get pods -n iron-namespace-nautilus
   ```
2. **Check the services**
   ```sh
   kubectl get svc -n iron-namespace-nautilus
   ```
![image](https://github.com/user-attachments/assets/a977a8e9-980f-4fa5-bf36-fb1714b68f81)

## Accessing the Application
- **Database (MariaDB) is only accessible within the cluster** (ClusterIP).
  - access it `http://<node-ip>:32678`

![image](https://github.com/user-attachments/assets/a5238913-f1f3-4fbe-a581-01f4ec47899c)
- **check logs**
  ```bash
  k logs -n iron-namespace-nautilus pod/iron-gallery-deployment-nautilus-656db68668-wx8t8
  k logs -n iron-namespace-nautilus pod/iron-db-deployment-nautilus-845dc8b44c-n7s5w
  
  ```
    **pod/iron-gallery-deployment-nautilus-656db68668-wx8t8**
    ![image](https://github.com/user-attachments/assets/2f931883-15e5-4219-8ab5-1f525c336e32)

   **pod/iron-db-deployment-nautilus-845dc8b44c-n7s5w**
   ![image](https://github.com/user-attachments/assets/749006e4-435b-4029-88f0-962138fac718)




