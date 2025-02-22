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

