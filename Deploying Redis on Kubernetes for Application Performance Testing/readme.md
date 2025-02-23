# 📝 Redis Deployment Task for Kubernetes

The Nautilus application development team identified performance issues in an application running on a Kubernetes cluster. After evaluating various factors, they recommended implementing an in-memory caching solution for the database service. Following several discussions, the team settled on using Redis. For now, they plan to deploy Redis on the Kubernetes cluster for testing, with the intention of moving it to production later. Below are the specifics of the task:

## 🎯 Create a Redis deployment with the following requirements:

- **Create a ConfigMap named my-redis-config with a redis-config key setting maxmemory to 2mb.** 🛠️  
- **The deployment should be named redis-deployment, use the redis:alpine image, and have a container named redis-container.** 🚀  
- **Ensure it runs with only 1 replica.** 🔢  
- **The container must request 1 CPU.** ⚙️  
- **Mount two volumes:** 💾  
  a. **An empty directory volume named data, mounted at /redis-master-data.** 📂  
  b. **A ConfigMap volume named redis-config, mounted at /redis-master, sourced from the my-redis-config ConfigMap.** 📋  
- **Expose port 6379 on the container.** 🌐  
- **Ensure the redis-deployment is fully operational and running** ✅  


## Implementation 

# Redis Deployment and ConfigMap Implementation 🛠️

This guide outlines how to create a ConfigMap for Redis, deploy Redis with Kubernetes, and configure it with a `maxmemory` setting of 2MB. The deployment will be configured with 1 replica and 1 CPU request, and it will mount two volumes for Redis data and configuration.

---

### Step 1: Create the ConfigMap for Redis 🛠️

We will start by creating a ConfigMap called `my-redis-config`, which will hold the Redis configuration for setting `maxmemory` to `2mb`.

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: my-redis-config
data:
  redis-config: "maxmemory 2mb"
```

Once you have saved this file as `my-redis-config.yaml`, apply the configuration:

```bash
kubectl apply -f my-redis-config.yaml
```

---

### Step 2: Create the Redis Deployment 🚀

Now, we'll create a `redis-deployment.yaml` file to deploy Redis with Kubernetes. It will use the `redis:alpine` image, request 1 CPU, and mount two volumes (`emptyDir` for Redis data and `ConfigMap` for Redis configuration).

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: redis-deployment
spec:
  replicas: 1
  selector:
    matchLabels:
      app: redis
  template:
    metadata:
      labels:
        app: redis
    spec:
      containers:
        - name: redis-container
          image: redis:alpine
          ports:
            - containerPort: 6379
          volumeMounts:
            - name: data
              mountPath: /redis-master-data
            - name: redis-config
              mountPath: /redis-master
          resources:
            requests:
              cpu: "1"
      volumes:
        - name: data
          emptyDir: {}
        - name: redis-config
          configMap:
            name: my-redis-config
```

After saving the file, apply the deployment with:

```bash
kubectl apply -f redis-deployment.yaml
```

---

### Step 3: Verify the Deployment ✅

To ensure the deployment is working correctly, use the following commands to check the status of your Redis deployment and pods:

```bash
kubectl get deployments
kubectl get pods
```

You should see a single replica of the `redis-deployment` running.

![image](https://github.com/user-attachments/assets/6b4a25ae-4dfe-473e-a6ed-4b20faeca308)

---

# Final Summary 📝

- **ConfigMap**: `my-redis-config` with Redis `maxmemory` set to `2mb`.
- **Deployment**: `redis-deployment` with 1 replica and 1 CPU request.
- **Volumes**: 
  - `data`: `emptyDir` volume for Redis data.
  - `redis-config`: ConfigMap volume for Redis configuration.
- **Redis Container**: Uses `redis:alpine` image and exposes port `6379`.



---

