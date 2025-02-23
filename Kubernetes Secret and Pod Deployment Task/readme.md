---

# 🛠️ Kubernetes Secret and Pod Deployment Task

The Nautilus DevOps team is working to deploy tools in a Kubernetes cluster. Some tools require licenses, which must be stored securely using Kubernetes secrets. Below are the detailed requirements and implementation steps.

---

## 📋 Task Requirements

### 🔑 1. Create a Generic Secret
- **Objective**: Securely store license information
- **Details**:
  - Source file: `blog.txt` located at `/opt` on the jump host
  - Secret name: `blog`
  - Content: Password/license-number from `blog.txt`

### 🖥️ 2. Create a Pod
- **Objective**: Deploy a pod to utilize the secret
- **Details**:
  - Pod name: `secret-datacenter`
  - Container name: `secret-container-datacenter`
  - Image: `fedora:latest` (explicitly use `latest` tag)
  - Command: Keep container running with `sleep`

### 🔗 3. Mount the Secret
- **Objective**: Make the secret accessible in the container
- **Details**:
  - Mount path: `/opt/games` inside the container
  - Source: Use the `blog` secret

### ✅ 4. Verification
- **Steps**:
  - Exec into `secret-container-datacenter`
  - Verify the secret key at `/opt/games`
- **Note**: Pod must be running for validation

---

## ⚙️ Implementation

### 🔑 Step 1: Create Generic Secret
Create a secret using the `kubectl` CLI:
```bash
kubectl create secret generic blog --from-file=/opt/blog.txt
```
Verify the secret exists:
```bash
kubectl get secrets
```

**Screenshot**: Secret Creation Output  
![Secret Creation](https://github.com/user-attachments/assets/f817fe66-a636-479c-bfb0-66b4cceca20f)  
*Caption: Output of `kubectl get secrets` showing the `blog` secret.*

---

### 🖥️ Step 2: Create Pod with Secret Mounted
Define the pod in a YAML file (e.g., `secret-pod.yaml`):
```yaml
apiVersion: v1
kind: Pod
metadata:
  name: secret-datacenter
spec:
  volumes:
  - name: secret-datacenter-volume
    secret:
      secretName: blog
  containers:
  - name: secret-container-datacenter
    image: fedora:latest
    command: ["sleep", "50000"]
    ports:
    - containerPort: 80
    volumeMounts:
    - name: secret-datacenter-volume
      mountPath: /opt/games
```
Apply the configuration:
```bash
kubectl apply -f secret-pod.yaml
```

---

### ✅ Step 3: Verify the Pod and Secret
Check pod status (ensure it’s "Running"):
```bash
kubectl get po
```
Access the container and inspect the secret:
```bash
kubectl exec -it secret-datacenter -- /bin/bash
ls /opt/games
cat /opt/games/blog.txt
```

**Screenshot**: Secret Verification in Container  
![Secret Verification](https://github.com/user-attachments/assets/f078e124-16ae-40ff-8c8e-93a9d97d239f)  
*Caption: Output of `cat /opt/games/blog.txt` inside the container.*

---

## ⚠️ Important Notes
- Ensure all pods are in a **running state** before submission.
- Validation may take some time—be patient!

---






