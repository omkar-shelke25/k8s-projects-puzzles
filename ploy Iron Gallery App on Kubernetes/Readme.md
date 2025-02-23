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

