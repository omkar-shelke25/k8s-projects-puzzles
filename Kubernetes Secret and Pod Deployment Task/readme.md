
# 🛠️ Kubernetes Secret and Pod Deployment Task

The Nautilus DevOps team is working to deploy tools in a Kubernetes cluster. Some tools require licenses, which must be stored securely using Kubernetes secrets. Below are the detailed requirements:

## 📋 Task Requirements

### 🔑 1. Create a Generic Secret
- **Objective**: Create a secret to store license information
- **Details**:
  - Source file: `blog.txt` located at `/opt` on jump host
  - Secret name: `blog`
  - Content: Password/license-number from `blog.txt`

### 🖥️ 2. Create a Pod
- **Objective**: Deploy a pod to consume the secret
- **Details**:
  - Pod name: `secret-datacenter`
  - Container name: `secret-container-datacenter`
  - Image: `fedora` (use `latest` tag explicitly)
  - Command: Use `sleep` to keep container running (e.g., `sleep infinity`)

### 🔗 3. Mount the Secret
- **Objective**: Make the secret available inside the container
- **Details**:
  - Mount path: `/opt/games` within the container
  - Source: Use the `blog` secret created earlier

### ✅ 4. Verification
- **Steps**:
  - Exec into the container `secret-container-datacenter`
  - Check for the secret key at `/opt/games`
- **Note**: Ensure the pod is in a running state before validation

