## Problem Statement: Deploying a Jekyll SSG Application on Kubernetes

The objective is to deploy a Jekyll Static Site Generator (SSG) application within a Kubernetes cluster, specifically in the `development` namespace. The deployment must ensure a fully functional application with proper user access, resource management, persistent storage, and external accessibility. The implementation requires precise configuration of Kubernetes resources, Role-Based Access Control (RBAC), and user authentication to meet the following detailed requirements:

## Architecture Diagram 📊

The following diagram illustrates the architecture of the Jekyll SSG deployment, highlighting the relationships between the pod, service, storage, and RBAC components:

![image](https://github.com/user-attachments/assets/a9c00968-97c1-468c-b4bd-ecb2691febd3)



1. **User and Kubeconfig Configuration** 🔑:
   - Configure a user named `martin` in the default kubeconfig file, utilizing a client key at `/root/martin.key` and a client certificate at `/root/martin.crt`. These files must be referenced externally, not embedded in the kubeconfig.
   - Establish a Kubernetes context named `developer` that links the user `martin` to the cluster `kubernetes` and sets the default namespace to `development`.
   - Set the `developer` context as the active context to ensure all subsequent operations are executed with the correct user and namespace.

2. **Role-Based Access Control (RBAC)** 🔒:
   - Create a role named `developer-role` in the `development` namespace, granting full permissions (`*`) to the following resources:
     - `services`
     - `persistentvolumeclaims`
     - `pods`
   - Define a role binding named `developer-rolebinding` in the `development` namespace to associate the `developer-role` with the user `martin`, ensuring the user has the necessary permissions to manage the specified resources.

3. **Service Configuration** 🌐:
   - Deploy a service named `jekyll` in the `development` namespace with the following specifications:
     - Expose `port: 8080` for internal cluster access.
     - Map to `targetPort: 4000`, corresponding to the container’s listening port.
     - Assign `nodePort: 30097` to enable external access via a cluster node’s IP.
     - Configure the service as type `NodePort` and ensure it selects the `jekyll` pod using the label `run=jekyll`.

4. **Pod Configuration** 🛠️:
   - Create a pod named `jekyll` in the `development` namespace, labeled with `run=jekyll` to match the service selector.
   - Include an init container named `copy-jekyll-site` with:
     - Image: `gcr.io/kodekloud/customimage/jekyll`.
     - Command: `["jekyll", "new", "/site"]` to initialize a new Jekyll site.
     - A volume mount named `site` at the path `/site` for storing the generated site.
   - Include a main container named `jekyll` with:
     - Image: `gcr.io/kodekloud/customimage/jekyll-serve` to serve the Jekyll site.
     - A volume mount named `site` at the path `/site` to access the generated site.
   - Define a volume named `site`, backed by a Persistent Volume Claim (PVC) named `jekyll-site`, to ensure persistence of the Jekyll site data across pod restarts.

5. **Storage Configuration** 💾:
   - Create a Persistent Volume Claim named `jekyll-pvc` (aliased as `jekyll-site`) in the `development` namespace to provide persistent storage for the `site` volume used by the `jekyll` pod.
   - Ensure the PVC is properly configured to support the storage needs of the Jekyll site.

6. **Namespace Resources** 🏷️:
   - Ensure the `development` namespace contains the following resources:
     - **Pod**: `jekyll`
     - **Service**: `jekyll` (also referred to as `jekyll-node-service`)
     - **Persistent Volume Claim**: `jekyll-pvc` (aliased as `jekyll-site`)
     - **Role**: `developer-role`
     - **Role Binding**: `developer-rolebinding`
     - **User**: `martin` (configured via kubeconfig and RBAC)
   - A Persistent Volume (`jekyll-pv`) is referenced but not detailed; assume it is pre-provisioned and linked to the `jekyll-pvc` if required.
   - The term `users` in the namespace context refers to the user `martin` configured for access.



## Deployment Goals 🎯

The deployment must achieve the following:
- **User Access**: Enable the user `martin` to manage resources in the `development` namespace with appropriate RBAC permissions.
- **Application Functionality**: Ensure the `jekyll` pod generates and serves the Jekyll site using the init and main containers.
- **Data Persistence**: Provide persistent storage via the `jekyll-pvc` to maintain the Jekyll site data.
- **Accessibility**: Expose the application through the `jekyll` service, accessible within the cluster and externally via the specified NodePort (`30097`).
- **Resource Organization**: Deploy all resources correctly in the `development` namespace, ensuring a cohesive and functional application environment.

---

## Project Structure

```
jekyll-k8s-deployment/
├── kubeconfig/
│   └── setup-kubeconfig.sh
├── manifests/
│   ├── 01-role.yaml
│   ├── 02-rolebinding.yaml
│   ├── 03-pvc.yaml
│   ├── 04-pod.yaml
│   └── 05-service.yaml
└── apply-all.sh
```

---

## Solution

### 1. Kubeconfig Configuration

**File: `kubeconfig/setup-kubeconfig.sh`**

```bash
#!/bin/bash
kubectl config set-credentials martin \
  --client-key=/root/martin.key \
  --client-certificate=/root/martin.crt

kubectl config set-context developer \
  --user=martin \
  --cluster=kubernetes \
  --namespace=development

kubectl config use-context developer
```

**Purpose**:
- Configures user `martin` with external key and certificate.
- Sets the `developer` context for the `kubernetes` cluster and `development` namespace.
- Activates the `developer` context.

---

### 2. Kubernetes Manifests

#### 2.1 Role

**File: `manifests/01-role.yaml`**

```yaml
apiVersion: rbac.authorization.k8s.io/v1
kind: Role
metadata:
  namespace: development
  name: developer-role
rules:
- apiGroups: [""]
  resources: ["services", "persistentvolumeclaims", "pods"]
  verbs: ["*"]
```

**Purpose**:
- Grants full permissions to `services`, `persistentvolumeclaims`, and `pods` in the `development` namespace.

#### 2.2 Role Binding

**File: `manifests/02-rolebinding.yaml`**

```yaml
apiVersion: rbac.authorization.k8s.io/v1
kind: RoleBinding
metadata:
  namespace: development
  name: developer-rolebinding
subjects:
- kind: User
  name: martin
  apiGroup: rbac.authorization.k8s.io
roleRef:
  kind: Role
  name: developer-role
  apiGroup: rbac.authorization.k8s.io
```

**Purpose**:
- Binds `developer-role` to user `martin`.

#### 2.3 Persistent Volume Claim

**File: `manifests/03-pvc.yaml`**

```yaml
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  namespace: development
  name: jekyll-site
spec:
  accessModes:
    - ReadWriteMany
  resources:
    requests:
      storage: 1Gi
```

**Purpose**:
- Defines `jekyll-site` PVC with `ReadWriteMany` access mode for multi-pod access.
- Requests 1Gi storage.

#### 2.4 Pod

**File: `manifests/04-pod.yaml`**

```yaml
apiVersion: v1
kind: Pod
metadata:
  namespace: development
  name: jekyll
  labels:
    run: jekyll
spec:
  initContainers:
  - name: copy-jekyll-site
    image: gcr.io/kodekloud/customimage/jekyll
    command: ["jekyll", "new", "/site"]
    volumeMounts:
    - name: site
      mountPath: /site
  containers:
  - name: jekyll
    image: gcr.io/kodekloud/customimage/jelly-serve
    ports:
    - containerPort: 4000
    volumeMounts:
    - name: site
      mountPath: /site
  volumes:
  - name: site
    persistentVolumeClaim:
      claimName: jekyll-site
```

**Purpose**:
- Runs an init container to generate the Jekyll site.
- Runs a main container to serve the site on port `4000`.
- Uses `jekyll-site` PVC for persistent storage.

#### 2.5 Service

**File: `manifests/05-service.yaml`**

```yaml
apiVersion: v1
kind: Service
metadata:
  namespace: development
  name: jekyll
spec:
  selector:
    run: jekyll
  ports:
  - protocol: TCP
    port: 8080
    targetPort: 4000
    nodePort: 30097
  type: NodePort
```

**Purpose**:
- Exposes the `jekyll` pod internally on port `8080` and externally on `nodePort: 30097`.

---

### 3. Deployment Script

**File: `apply-all.sh`**

```bash
#!/bin/bash
kubectl apply -f manifests/01-role.yaml
kubectl apply -f manifests/02-rolebinding.yaml
kubectl apply -f manifests/03-pvc.yaml
kubectl apply -f manifests/04-pod.yaml
kubectl apply -f manifests/05-service.yaml
```

**Purpose**:
- Applies all manifests in order.

---

## Deployment Steps

1. **Set up kubeconfig**:
   ```bash
   chmod +x kubeconfig/setup-kubeconfig.sh
   ./kubeconfig/setup-kubeconfig.sh
   ```

2. **Apply manifests**:
   ```bash
   chmod +x apply-all.sh
   ./apply-all.sh
   ```

3. **Verify**:
   ```bash
   kubectl get all,pvc,role,rolebinding -n development
   ```

4. **Access**:
   - External: `http://<node-ip>:30097`

---
