### 🛠️ **Task: Securely Store License Information in Kubernetes Secrets**  

The **Nautilus DevOps** team is deploying tools in a Kubernetes cluster, and some tools require **license-based authentication**. To securely store license information, the team wants to utilize **Kubernetes Secrets**.  

---  

## 📌 **Task Details**  

### 🔐 **1. Create a Kubernetes Secret**  
- A **secret key file** named `blog.txt` is located at `/opt/blog.txt` on the **jump host**.  
- The file contains the following **license key/password**:  
  ```sh
  cat /opt/blog.txt
  5ecur3
  ```
- Create a **Kubernetes Secret** named **`blog`** using the data from `blog.txt`.  

---

### 🖥️ **2. Deploy a Pod to Consume the Secret**  
- Create a **pod** named **`secret-datacenter`**.  
- Configure the **pod's specification** with:  
  - **Container Name:** `secret-container-datacenter`  
  - **Image:** `fedora:latest`  
  - The container should run indefinitely using the `sleep` command.  

---

### 📂 **3. Mount the Secret Inside the Container**  
- The **created secret** must be **mounted** inside the **container** at the path `/opt/games`.  
- The mounted secret should be accessible as a file inside this directory.  

---

### ✅ **4. Verification Steps**  
- Once the pod is running, verify that the secret is correctly mounted by:  
  1. **Executing into the container** and navigating to `/opt/games`.  
  2. **Checking if the secret file exists** inside the mounted directory.  
  3. **Reading the contents of the file** to confirm it contains the correct license key/password.  

**Important Notes:**  
✔ Ensure the **pod is in a running state** before validation.  
✔ **Verification may take time**, so be patient before checking the task completion.  

## Implementation
![image](https://github.com/user-attachments/assets/f817fe66-a636-479c-bfb0-66b4cceca20f)
![image](https://github.com/user-attachments/assets/f078e124-16ae-40ff-8c8e-93a9d97d239f)


