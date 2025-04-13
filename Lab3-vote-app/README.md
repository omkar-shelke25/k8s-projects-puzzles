### Problem Statement

Design and implement a Kubernetes-based voting application with the following components and requirements:

1. **Vote Service and Deployment**:
   - Create a service named `vote-service` exposing port `5000`, targeting port `80`, with a `NodePort` of `31000`.
   - The service must expose a deployment named `vote-deployment`.
   - Create a deployment named `vote-deployment` using the image `kodekloud/examplevotingapp_vote:before`, ensuring the status is `Running`.

2. **Redis Service and Deployment**:
   - Create a service named `redis` of type `ClusterIP`, exposing port `6379` and targeting port `6379`.
   - The service must expose a deployment named `redis-deployment`.
   - Create a deployment named `redis-deployment` using the image `redis:alpine`, with an `EmptyDir` volume named `redis-data` mounted at `/data`, ensuring the status is `Running`.

3. **Worker Deployment**:
   - Create a deployment named `worker` using the image `kodekloud/examplevotingapp_worker`, ensuring the status is `Running`.

4. **Database Service and Deployment**:
   - Create a service named `db` of type `ClusterIP`, exposing port `5432` and targeting port `5432`.
   - Create a deployment named `db-deployment` using the image `postgres:9.4`, with an environment variable `POSTGRES_HOST_AUTH_METHOD=trust`, an `EmptyDir` volume named `db-data` mounted at `/var/lib/postgresql/data`, ensuring the status is `Running`.

5. **Result Deployment and Service**:
   - Create a deployment named `result-deployment` using the image `kodekloud/examplevotingapp_result:before`, ensuring the status is `Running`.
   - Create a service for `result-deployment` exposing port `5001`, targeting port `80`, with a `NodePort` of `31001`.

6. **General Requirements**:
   - Ensure all deployments are in a `Running` state.
   - Ensure services correctly expose their respective deployments.
   - All components should be deployed in the `vote` namespace.



## Architecture Overview

The voting application consists of:
- **Vote Service/Deployment**: Frontend UI for users to cast votes, accessible via `NodePort 31000`.
- **Redis Service/Deployment**: In-memory storage for vote data, using an `EmptyDir` volume.
- **Worker Deployment**: Processes votes and stores them in the database.
- **Database Service/Deployment**: PostgreSQL database for persistent storage, with an `EmptyDir` volume.
- **Result Service/Deployment**: Displays voting results, accessible via `NodePort 31001`.
 ![image](https://github.com/user-attachments/assets/28ec7667-2873-4ceb-8eec-a986c5e1905c)

## Solution

Below is the implementation of the Kubernetes manifests based on the provided requirements and the repository file structure.

#### 1. Vote Service and Deployment

**vote-service.yaml**:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: vote-service
  namespace: vote
spec:
  type: NodePort
  ports:
    - port: 5000
      targetPort: 80
      nodePort: 31000
  selector:
    app: vote
```

**vote-deployment.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: vote-deployment
  namespace: vote
spec:
  replicas: 1
  selector:
    matchLabels:
      app: vote
  template:
    metadata:
      labels:
        app: vote
    spec:
      containers:
        - name: vote
          image: kodekloud/examplevotingapp_vote:before
          ports:
            - containerPort: 80
```

#### 2. Redis Service and Deployment

**redis-service.yaml**:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: redis
  namespace: vote
spec:
  type: ClusterIP
  ports:
    - port: 6379
      targetPort: 6379
  selector:
    app: redis
```

**redis-deployments.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: redis-deployment
  namespace: vote
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
        - name: redis
          image: redis:alpine
          ports:
            - containerPort: 6379
          volumeMounts:
            - name: redis-data
              mountPath: /data
      volumes:
        - name: redis-data
          emptyDir: {}
```

#### 3. Worker Deployment

**worker.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: worker
  namespace: vote
spec:
  replicas: 1
  selector:
    matchLabels:
      app: worker
  template:
    metadata:
      labels:
        app: worker
    spec:
      containers:
        - name: worker
          image: kodekloud/examplevotingapp_worker
```

#### 4. Database Service and Deployment

**db-svc.yaml**:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: db
  namespace: vote
spec:
  type: ClusterIP
  ports:
    - port: 5432
      targetPort: 5432
  selector:
    app: db
```

**db-deployments.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: db-deployment
  namespace: vote
spec:
  replicas: 1
  selector:
    matchLabels:
      app: db
  template:
    metadata:
      labels:
        app: db
    spec:
      containers:
        - name: db
          image: postgres:9.4
          ports:
            - containerPort: 5432
          env:
            - name: POSTGRES_HOST_AUTH_METHOD
              value: "trust"
          volumeMounts:
            - name: db-data
              mountPath: /var/lib/postgresql/data
      volumes:
        - name: db-data
          emptyDir: {}
```

#### 5. Result Deployment and Service

**result-deployment.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: result-deployment
  namespace: vote
spec:
  replicas: 1
  selector:
    matchLabels:
      app: result
  template:
    metadata:
      labels:
        app: result
    spec:
      containers:
        - name: result
          image: kodekloud/examplevotingapp_result:before
          ports:
            - containerPort: 80
```

**result-service.yaml**:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: result-service
  namespace: vote
spec:
  type: NodePort
  ports:
    - port: 5001
      targetPort: 80
      nodePort: 31001
  selector:
    app: result
```

#### 6. Deployment Steps

1. **Create Namespace**:
   ```bash
   kubectl create namespace vote
   ```

2. **Apply Manifests**:
   ```bash
   kubectl apply -f vote-service.yaml
   kubectl apply -f vote-deployment.yaml
   kubectl apply -f redis-service.yaml
   kubectl apply -f redis-deployments.yaml
   kubectl apply -f worker.yaml
   kubectl apply -f db-svc.yaml
   kubectl apply -f db-deployments.yaml
   kubectl apply -f result-deployment.yaml
   kubectl apply -f result-service.yaml
   ```

3. **Verify Deployment**:
   ```bash
   kubectl get all -n vote
   ```
 
![image](https://github.com/user-attachments/assets/57171854-9c13-4728-96fa-a61ae3a3d49a)

4. **Voting User ui**
![image](https://github.com/user-attachments/assets/7fbc8777-c00b-47e6-ad1e-f1a151ce7e0d)

5. **Voting User Ui**
![image](https://github.com/user-attachments/assets/4c664e75-f77c-4a6d-baa6-b17c83509096)





