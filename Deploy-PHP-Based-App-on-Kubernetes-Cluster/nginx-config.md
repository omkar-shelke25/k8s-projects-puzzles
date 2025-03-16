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
