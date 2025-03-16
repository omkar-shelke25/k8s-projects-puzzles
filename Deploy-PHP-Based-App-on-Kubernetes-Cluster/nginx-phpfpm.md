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
