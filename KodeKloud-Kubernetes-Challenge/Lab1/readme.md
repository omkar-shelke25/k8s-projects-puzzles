## Problem Statement: Deploying a Jekyll SSG Application on Kubernetes

The objective is to deploy a Jekyll Static Site Generator (SSG) application within a Kubernetes cluster, specifically in the `development` namespace. The deployment must ensure a fully functional application with proper user access, resource management, persistent storage, and external accessibility. The implementation requires precise configuration of Kubernetes resources, Role-Based Access Control (RBAC), and user authentication to meet the following detailed requirements:

## Architecture Diagram 📊

The following diagram illustrates the architecture of the Jekyll SSG deployment, highlighting the relationships between the pod, service, storage, and RBAC components:

![image](https://github.com/user-attachments/assets/8de5e144-4bdb-4299-9dcc-7f3f9f75648a)


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

The implementation must adhere to Kubernetes best practices, ensuring accurate resource definitions, secure user authentication, and reliable storage management to deliver a fully operational Jekyll SSG application.

--- 


To deploy the Jekyll Static Site Generator (SSG) application as per the provided architecture and requirements, I'll guide you through creating the necessary Kubernetes configuration files, ensuring proper organization and clarity. Below is a structured approach with YAML files, explanations, and a README to set up the deployment in the `development` namespace. The files will include appropriate icons for clarity in the README.

---

## Project Structure

We'll organize the configuration files in a directory named `jekyll-deployment` with the following structure:

```
jekyll-deployment/
├── kubeconfig/
│   └── kubeconfig.yaml         📜
├── manifests/
│   ├── namespace.yaml         🏷️
│   ├── rbac.yaml             🔒
│   ├── pvc.yaml              💾
│   ├── pod.yaml              🛠️
│   └── service.yaml          🌐
└── README.md                 📖
```

- **`kubeconfig/`**: Contains the kubeconfig file for user configuration.
- **`manifests/`**: Contains Kubernetes resource definitions.
- **`README.md`**: Provides instructions for deployment.

---

## Step-by-Step Configuration

### 1. Kubeconfig Setup (`kubeconfig.yaml`) 📜

This file configures the user `martin`, sets up the `developer` context, and references the provided key and certificate files.

```yaml
# kubeconfig/kubeconfig.yaml
apiVersion: v1
kind: Config
clusters:
- cluster:
    server: https://kubernetes.default.svc
    certificate-authority: /path/to/ca.crt  # Adjust to your cluster's CA
  name: kubernetes
users:
- name: martin
  user:
    client-certificate: /root/martin.crt
    client-key: /root/martin.key
contexts:
- context:
    cluster: kubernetes
    namespace: development
    user: martin
  name: developer
current-context: developer
```

**Notes**:
- Replace `/path/to/ca.crt` with the actual path to your cluster's CA certificate.
- The `client-certificate` and `client-key` paths point to `/root/martin.crt` and `/root/martin.key` as specified.
- The `developer` context is set as the current context, targeting the `development` namespace.

### 2. Namespace (`namespace.yaml`) 🏷️

Create the `development` namespace to house all resources.

```yaml
# manifests/namespace.yaml
apiVersion: v1
kind: Namespace
metadata:
  name: development
```

### 3. RBAC Configuration (`rbac.yaml`) 🔒

Define the `developer-role` and `developer-rolebinding` to grant `martin` the required permissions.

```yaml
# manifests/rbac.yaml
---
apiVersion: rbac.authorization.k8s.io/v1
kind: Role
metadata:
  namespace: development
  name: developer-role
rules:
- apiGroups: [""]
  resources: ["services", "persistentvolumeclaims", "pods"]
  verbs: ["*"]
---
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

**Notes**:
- The `Role` grants full permissions (`*`) to `services`, `persistentvolumeclaims`, and `pods`.
- The `RoleBinding` associates the `developer-role` with the user `martin`.

### 4. Persistent Volume Claim (`pvc.yaml`) 💾

Create the `jekyll-pvc` (aliased as `jekyll-site`) for persistent storage.

```yaml
# manifests/pvc.yaml
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  namespace: development
  name: jekyll-pvc
spec:
  accessModes:
    - ReadWriteOnce
  resources:
    requests:
      storage: 1Gi  # Adjust size as needed
  storageClassName: standard  # Adjust to your cluster's storage class
```

**Notes**:
- The PVC is named `jekyll-pvc` and will be referenced as `jekyll-site` in the pod.
- Assumes a `standard` storage class; modify based on your cluster's configuration.
- The size (`1Gi`) is a placeholder; adjust as needed.

### 5. Pod Configuration (`pod.yaml`) 🛠️

Define the `jekyll` pod with the init and main containers, using the `jekyll-site` volume.

```yaml
# manifests/pod.yaml
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
    image: gcr.io/kodekloud/customimage/jekyll-serve
    volumeMounts:
    - name: site
      mountPath: /site
  volumes:
  - name: site
    persistentVolumeClaim:
      claimName: jekyll-pvc
```

**Notes**:
- The init container (`copy-jekyll-site`) generates a new Jekyll site at `/site`.
- The main container (`jekyll`) serves the site, mounting the same `/site` path.
- The `site` volume uses the `jekyll-pvc` PVC for persistence.
- The pod is labeled `run=jekyll` for service selection.

### 6. Service Configuration (`service.yaml`) 🌐

Expose the `jekyll` pod via a `NodePort` service.

```yaml
# manifests/service.yaml
apiVersion: v1
kind: Service
metadata:
  namespace: development
  name: jekyll
spec:
  type: NodePort
  ports:
  - port: 8080
    targetPort: 4000
    nodePort: 30097
  selector:
    run: jekyll
```


