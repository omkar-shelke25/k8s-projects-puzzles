# Problem Statement: Deploy PHP-Based Application on Kubernetes Cluster

The Nautilus Application Development team is tasked with deploying a PHP-based application on a Kubernetes cluster. Following discussions with the DevOps team, the deployment will utilize `nginx` as the web server and `php-fpm` for PHP processing. The team has also outlined specific custom configuration requirements for this deployment. Your task is to create the necessary Kubernetes manifests to meet the requirements listed below. Ensure all components are correctly configured and the application is accessible post-deployment.

## Requirements:

1. **Service Configuration:**
   - Create a Kubernetes `Service` to expose the application.
   - Service type must be `NodePort`.
   - Assign the `nodePort` as `30012`.

2. **ConfigMap for Nginx Configuration:**
   - Create a `ConfigMap` named `nginx-config` to store a custom `nginx.conf` file.
   - Apply the following changes to the default `nginx.conf`:
     a. Change the default port from `80` to `8092`.
     b. Update the default document root from `/usr/share/nginx` to `/var/www/html`.
     c. Set the directory index to `index index.html index.htm index.php`.

3. **Pod Configuration:**
   - Create a single `Pod` named `nginx-phpfpm`.
   - The pod must include two containers: one for `nginx` and one for `php-fpm`.
   - Additional configurations:
     a. **Shared Volume:**
        - Create an `emptyDir` volume named `shared-files`.
        - This volume must be shared between the `nginx` and `php-fpm` containers.
     b. **ConfigMap Volume:**
        - Map the `nginx-config` ConfigMap as a volume for the `nginx` container.
        - Name the volume `nginx-config-volume`.
        - Mount it to `/etc/nginx/nginx.conf` with `subPath` set to `nginx.conf`.
     c. **Container Specifications:**
        - **Nginx Container:**
          - Name: `nginx-container`.
          - Image: `nginx:latest`.
        - **PHP-FPM Container:**
          - Name: `php-fpm-container`.
          - Image: `php:8.1-fpm-alpine`.
     d. **Volume Mounts:**
        - Mount the `shared-files` volume to `/var/www/html` in both containers.
     e. **File Copy:**
        - Copy the file `/opt/index.php` from the jump host to the nginx document root (`/var/www/html`) inside the `nginx` container.
        - Ensure the application is accessible via the "App" button on the top bar after deployment.

## Additional Instructions:
- Use any labels as per your preference for the resources.
- The `kubectl` utility on the jump host is pre-configured to interact with the Kubernetes cluster.
- Before finalizing the deployment, verify that all pods are in the `Running` state.

## Deliverables:
- Kubernetes manifests (YAML files) for the `Service`, `ConfigMap`, and `Pod`.
- Instructions or commands to copy `/opt/index.php` into the `nginx` container.
- Confirmation that the application is accessible via the specified `NodePort` (`30012`).

---

# Solution: Deploy PHP-Based Application on Kubernetes Cluster

The task is to deploy a PHP-based application on a Kubernetes cluster using `nginx` as the web server and `php-fpm` for PHP processing, adhering to specific configuration requirements. Below, I provide the complete Kubernetes manifests, commands to copy the application file, and verification steps to ensure the application is accessible via the specified `NodePort` (`30012`) and the "App" button on the top bar.

---

## Approach

To meet the requirements, we need to:

1. **Create a `Service`:** Expose the application using a `NodePort` service on port `30012`.
2. **Create a `ConfigMap`:** Store a custom `nginx.conf` file with modified settings for `nginx`.
3. **Create a `Pod`:** Define a pod with `nginx` and `php-fpm` containers, including shared volumes and custom configurations.
4. **Copy the Application File:** Transfer `index.php` from the jump host to the shared document root.
5. **Verify Deployment:** Ensure the pod is running and the application is accessible.

Let’s dive into the solution step by step.

---

## Kubernetes Manifests

### 1. ConfigMap: `nginx-config`
The `ConfigMap` stores the custom `nginx.conf` file with the following changes:
- **Port:** Changed from `80` to `8092`.
- **Document Root:** Updated from `/usr/share/nginx` to `/var/www/html`.
- **Index Directive:** Set to `index index.html index.htm index.php`.
- **PHP Processing:** Configured to pass `.php` requests to `php-fpm` at `127.0.0.1:9000`.

Here’s the YAML file:

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: nginx-config
  # 📝 Stores the custom nginx configuration
data:
  nginx.conf: |
    events {}
    http {
      server {
        listen 8092;                     # 🔧 Changed from default port 80
        root /var/www/html;              # 📂 Updated document root
        index index.html index.htm index.php; # 📑 Added index.php to directory index
        location ~ \.php$ {              # ⚙️ PHP file processing
          fastcgi_pass 127.0.0.1:9000;   # ➡️ Forward to php-fpm
          fastcgi_index index.php;
          include fastcgi_params;
          fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
      }
    }
```

### 2. Pod: `nginx-phpfpm`
The pod contains two containers (`nginx` and `php-fpm`) with shared volumes:
- **Shared Volume (`shared-files`):** An `emptyDir` volume mounted to `/var/www/html` in both containers.
- **ConfigMap Volume (`nginx-config-volume`):** Mounts the `nginx.conf` file to `/etc/nginx/nginx.conf` in the `nginx` container.

Here’s the YAML file:

```yaml
apiVersion: v1
kind: Pod
metadata:
  name: nginx-phpfpm
  labels:
    app: nginx-phpfpm              # 🏷️ Label for service selector
  # 📦 Single pod hosting nginx and php-fpm
spec:
  containers:
  - name: nginx-container          # 🌐 Nginx web server
    image: nginx:latest
    ports:
    - containerPort: 8092          # 🔌 Exposes nginx port
    volumeMounts:
    - name: shared-files           # 📂 Shared document root
      mountPath: /var/www/html
    - name: nginx-config-volume    # ⚙️ Custom nginx config
      mountPath: /etc/nginx/nginx.conf
      subPath: nginx.conf
  - name: php-fpm-container        # 🛠️ PHP processing
    image: php:8.1-fpm-alpine
    volumeMounts:
    - name: shared-files           # 📂 Shared document root
      mountPath: /var/www/html
  volumes:
  - name: shared-files             # 🔗 EmptyDir for file sharing
    emptyDir: {}
  - name: nginx-config-volume      # 📜 ConfigMap volume for nginx.conf
    configMap:
      name: nginx-config
```

### 3. Service: `nginx-phpfpm-service`
The `Service` exposes the `nginx` container on `NodePort` `30012`, targeting port `8092` on the pod.

Here’s the YAML file:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: nginx-phpfpm-service
  # 🌍 Exposes the application externally
spec:
  type: NodePort                   # 🔗 NodePort service type
  ports:
  - port: 8092                    # 🔌 Internal service port
    targetPort: 8092              # 🎯 Targets nginx container port
    nodePort: 30012               # 🌐 External access port
  selector:
    app: nginx-phpfpm             # 🏷️ Matches pod label
```

---

## Deployment Steps

### 1. Apply the Manifests
Run these commands on the jump host (where `kubectl` is configured) to deploy the resources:

```bash
kubectl apply -f configmap.yaml    # 📜 Create ConfigMap first (dependency for Pod)
kubectl apply -f pod.yaml          # 📦 Deploy the Pod
kubectl apply -f service.yaml      # 🌍 Expose the application
```

### 2. Copy `index.php`
Copy the `index.php` file from `/opt/index.php` on the jump host to the `nginx` container’s document root:

```bash
kubectl cp /opt/index.php nginx-phpfpm:/var/www/html/index.php -c nginx-container
# 📤 Copies index.php to the shared volume
```

Since both containers share the `shared-files` volume at `/var/www/html`, the file becomes accessible to both `nginx` (to serve) and `php-fpm` (to process).

---

## Verification

### 1. Check Pod Status
Ensure the pod is running:

```bash
kubectl get pods
```

Expected output:
```
NAME           READY   STATUS    RESTARTS   AGE
nginx-phpfpm   2/2     Running   0          5m
```
![image](https://github.com/user-attachments/assets/d14f7b1c-5f77-4cab-ad56-e67b5aef1f2e)
![image](https://github.com/user-attachments/assets/3d61cdd6-9722-4ca9-97d1-1aa784e0e424)
- **✅ Icon:** If `READY` shows `2/2` and `STATUS` is `Running`, both containers are operational.

### 2. Access the Application
- **Via NodePort:** The application should be accessible at `<node-ip>:30012`, where `<node-ip>` is the IP address of the Kubernetes node.
- **Via "App" Button:** Assuming the platform maps the "App" button to the `NodePort` service, clicking it should load the application (served by `index.php`).

If the pod isn’t running, troubleshoot with:
```bash
kubectl describe pod nginx-phpfpm  # 🔍 Detailed pod info
kubectl logs nginx-phpfpm -c nginx-container  # 📜 Nginx logs
kubectl logs nginx-phpfpm -c php-fpm-container  # 📜 PHP-FPM logs
```

---

## Explanation

- **ConfigMap:** The custom `nginx.conf` ensures `nginx` listens on `8092`, serves files from `/var/www/html`, and forwards PHP requests to `php-fpm` on `127.0.0.1:9000`. Since both containers share the pod’s network namespace, `localhost` communication works seamlessly.
- **Pod:** 
  - `nginx-container` uses the `nginx:latest` image and mounts the custom config and shared volume.
  - `php-fpm-container` uses `php:8.1-fpm-alpine`, which listens on port `9000` by default, and mounts the shared volume.
- **Service:** The `NodePort` type exposes the application externally on port `30012`, mapped to the `nginx` container’s port `8092`.
- **File Copy:** The `kubectl cp` command places `index.php` in the shared volume, making it the entry point for the application.

---

## Deliverables

1. **Kubernetes Manifests:** Provided above as `configmap.yaml`, `pod.yaml`, and `service.yaml`.
2. **Command to Copy `index.php`:** `kubectl cp /opt/index.php nginx-phpfpm:/var/www/html/index.php -c nginx-container`.
3. **Confirmation:** Post-deployment, the application is accessible via `<node-ip>:30012` and the "App" button, assuming the pod is running and the platform’s UI is configured to use the `NodePort`.


