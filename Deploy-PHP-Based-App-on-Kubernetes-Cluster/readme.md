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

## Solution (Kubernetes Manifests)

Below is an example of the Kubernetes manifests based on the problem statement:

### 1. ConfigMap: `nginx-config`
```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: nginx-config
data:
  nginx.conf: |
    events {}
    http {
      server {
        listen 8092;
        root /var/www/html;
        index index.html index.htm index.php;
        location ~ \.php$ {
          fastcgi_pass 127.0.0.1:9000;
          fastcgi_index index.php;
          include fastcgi_params;
          fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
      }
    }
```

### 2. Pod: `nginx-phpfpm`
```yaml
apiVersion: v1
kind: Pod
metadata:
  name: nginx-phpfpm
  labels:
    app: nginx-phpfpm
spec:
  containers:
  - name: nginx-container
    image: nginx:latest
    ports:
    - containerPort: 8092
    volumeMounts:
    - name: shared-files
      mountPath: /var/www/html
    - name: nginx-config-volume
      mountPath: /etc/nginx/nginx.conf
      subPath: nginx.conf
  - name: php-fpm-container
    image: php:8.1-fpm-alpine
    volumeMounts:
    - name: shared-files
      mountPath: /var/www/html
  volumes:
  - name: shared-files
    emptyDir: {}
  - name: nginx-config-volume
    configMap:
      name: nginx-config
```

### 3. Service: `nginx-phpfpm-service`
```yaml
apiVersion: v1
kind: Service
metadata:
  name: nginx-phpfpm-service
spec:
  type: NodePort
  ports:
  - port: 8092
    targetPort: 8092
    nodePort: 30012
  selector:
    app: nginx-phpfpm
```

### 4. Commands to Copy `index.php`
After applying the manifests, copy the `index.php` file from the jump host to the `nginx` container:
```bash
kubectl cp /opt/index.php nginx-phpfpm:/var/www/html/index.php -c nginx-container
```

### 5. Verification
- Apply the manifests:
  ```bash
  kubectl apply -f configmap.yaml
  kubectl apply -f pod.yaml
  kubectl apply -f service.yaml
  ```
- Check pod status:
  ```bash
  kubectl get pods
  ```
![image](https://github.com/user-attachments/assets/d14f7b1c-5f77-4cab-ad56-e67b5aef1f2e)
![image](https://github.com/user-attachments/assets/3d61cdd6-9722-4ca9-97d1-1aa784e0e424)

- Ensure the pod `nginx-phpfpm` is in the `Running` state before proceeding.

