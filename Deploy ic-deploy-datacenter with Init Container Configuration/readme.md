# **Problem Statement: Deploying Applications with Init Containers**  

Certain applications need to be deployed on a Kubernetes cluster, but they require specific configurations before the main application starts. Since these changes cannot be made inside the container images, the DevOps team has decided to use **init containers** to handle the necessary pre-deployment tasks.  

To validate this approach, the team has designed the following test scenario:  

1. Create a **Deployment** named **`ic-deploy-nautilus`**.  
2. Set the **replica count** to **1**.  
3. Assign the **label** `app=ic-nautilus` to both the **Deployment** and the **Pod template metadata**.  

## **Init Container Configuration**  
4. Add an **init container** named **`ic-msg-nautilus`** using the **Ubuntu (latest)** image.  
5. Configure it to run the command:  
   ```sh
   /bin/bash -c "echo Init Done - Welcome to xFusionCorp Industries > /ic/blog"
   ```
6. Mount a **volume** named **`ic-volume-nautilus`** at **`/ic`** inside the **init container**.  

## **Main Application Container Configuration**  
7. Add a **main container** named **`ic-main-nautilus`**, also using the **Ubuntu (latest)** image.  
8. Configure it to run the command:  
   ```sh
   /bin/bash -c "while true; do cat /ic/blog; sleep 5; done"
   ```
9. Mount the same **`ic-volume-nautilus`** at **`/ic`** inside the **main container**.  

## **Volume Configuration**  
10. Create a **volume** named **`ic-volume-nautilus`** of type **emptyDir** to enable data sharing between the init and main containers.  

---


---

# 🚀 **Kubernetes Deployment with Init Container**

## 📝 **Overview**  
- **Init Container** → Creates a file with the message `"Welcome to xFusionCorp Industries"`.  
- **Main Container** → Reads and displays the message every **5 seconds**.  
- **Shared Volume** (`emptyDir`) → Used to transfer data between the containers.

---

## 📜 **Deployment Configuration**
``yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: ic-deploy-nautilus  # 🔹 Name of the deployment
spec:
  replicas: 1  # 🔹 Running only 1 replica
  selector:
    matchLabels:
      app: ic-nautilus
  template:
    metadata:
      labels:
        app: ic-nautilus
    spec:
      volumes:
        - name: ic-volume-nautilus  # 📂 Shared volume for both containers
          emptyDir: {}  # 🔹 Temporary storage that lasts as long as the pod
          
      initContainers:
        - name: ic-msg-nautilus  # 🚀 Init container (runs first)
          image: ubuntu:latest  # 🔹 Base image
          command: 
            - "/bin/bash"
            - "-c"
            - "echo 'Init Done - Welcome to xFusionCorp Industries' > /ic/blog"  # 📝 Writes message to shared volume
          volumeMounts:
            - name: ic-volume-nautilus
              mountPath: /ic  # 🔗 Mounts the shared volume

      containers:
        - name: ic-main-nautilus  # 🏗️ Main container
          image: ubuntu:latest
          command:
            - "/bin/bash" 
            - "-c"
            - "while true; do cat /ic/blog; sleep 5; done"  # 📜 Reads and prints the message every 5 seconds
          volumeMounts:
            - name: ic-volume-nautilus
              mountPath: /ic  # 🔗 Mounts the shared volume
```

---

## ⚡ **Deployment Commands**
```sh
# 🚀 Apply the deployment
kubectl apply -f deployment.yaml

# 📌 Check deployment and pod status
kubectl get deploy,po

# 📜 Get logs of the main container (replace <POD_NAME> with actual pod name)
kubectl logs pod/<POD_NAME>
```
> **💡 Tip:** Use `kubectl get pods` to find the actual pod name.

---
## OutPut ::

![image](https://github.com/user-attachments/assets/03ab1dfa-516b-40e9-90be-c63930782478)
