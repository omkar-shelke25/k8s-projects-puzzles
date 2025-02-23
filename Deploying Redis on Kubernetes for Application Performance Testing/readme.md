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

