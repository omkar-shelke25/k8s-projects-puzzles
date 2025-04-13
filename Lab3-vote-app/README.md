# **Problem Statement:**

Design and implement a Kubernetes-based voting application with the following components and requirements:

1. **Vote Service and Deployment:**
   - Create a service named `vote-service` exposing port `5000`, targeting port `80`, with a NodePort of `31000`.
   - The service must expose a deployment named `vote-deployment`.
   - Create a deployment named `vote-deployment` using the image `kodekloud/examplevotingapp_vote:before`, ensuring the status is `Running`.

2. **Redis Service and Deployment:**
   - Create a service named `redis` of type `ClusterIP`, exposing port `6379` and targeting port `6379`.
   - The service must expose a deployment named `redis-deployment`.
   - Create a deployment named `redis-deployment` using the image `redis:alpine`, with an EmptyDir volume named `redis-data` mounted at `/data`, ensuring the status is `Running`.

3. **Worker Deployment:**
   - Create a deployment named `worker` using the image `kodekloud/examplevotingapp_worker`, ensuring the status is `Running`.

4. **Database Service and Deployment:**
   - Create a service named `db` of type `ClusterIP`, exposing port `5432` and targeting port `5432`.
   - Create a deployment named `db-deployment` using the image `postgres:9.4`, with an environment variable `POSTGRES_HOST_AUTH_METHOD=trust`, an EmptyDir volume named `db-data` mounted at `/var/lib/postgresql/data`, ensuring the status is `Running`.

5. **Result Deployment and Service:**
   - Create a deployment named `result-deployment` using the image `kodekloud/examplevotingapp_result:before`, ensuring the status is `Running`.
   - Create a service for `result-deployment` exposing port `5001`, targeting port `80`, with a NodePort of `31001`.

Ensure all deployments are in a `Running` state and services correctly expose their respective deployments.

## Architecture Vote App
 ![image](https://github.com/user-attachments/assets/28ec7667-2873-4ceb-8eec-a986c5e1905c)


## final output
k get all -n vote
![image](https://github.com/user-attachments/assets/57171854-9c13-4728-96fa-a61ae3a3d49a)

## voting user ui
![image](https://github.com/user-attachments/assets/7fbc8777-c00b-47e6-ad1e-f1a151ce7e0d)

## voting result ui
![image](https://github.com/user-attachments/assets/4c664e75-f77c-4a6d-baa6-b17c83509096)


