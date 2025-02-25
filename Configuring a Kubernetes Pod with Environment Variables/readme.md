

# 📋 **Problem Statement**

There are a number of parameters that are used by the applications. We need to define these as environment variables, so that we can use them as needed within different configs. Below is a scenario which needs to be configured on Kubernetes cluster. Please find below more details about the same.

---

## 🛠️ **Task: Create a Pod**

Create a pod named envars.  
- **Container Name**: Container name should be fieldref-container, use image redis preferable latest tag, use command 'sh', '-c' and args should be  
   ```bash
  while true; do
      echo -en '\n';
      printenv NODE_NAME POD_NAME;
      printenv POD_IP POD_SERVICE_ACCOUNT;
      sleep 10;
  done;
  ```  
  *(Note: please take care of indentations)*

---

## 🌍 **Environment Variables**

Define Four environment variables as mentioned below:  
1. **🔧 a.)** The first env should be named as NODE_NAME, set valueFrom fieldref and fieldPath should be spec.nodeName.  
2. **📛 b.)** The second env should be named as POD_NAME, set valueFrom fieldref and fieldPath should be metadata.name.  
3. **🌐 c.)** The third env should be named as POD_IP, set valueFrom fieldref and fieldPath should be status.podIP.  
4. **🔑 d.)** The fourth env should be named as POD_SERVICE_ACCOUNT, set valueFrom fieldref and fieldPath shoulbe be spec.serviceAccountName.
5.🔄 **Restart Policy** :: Set restart policy to Never.

---

## ✅ **Verification**

You're on the right track! To check the environment variables inside the `envars` pod, you can follow these steps:

### 1️⃣ Check the Pod status  
First, ensure the pod is running:  
```sh
kubectl get pods
```

### 2️⃣ View logs (Optional)  
If the pod is already printing environment variables in the logs, check the logs:  
```sh
kubectl logs envars
```

### 3️⃣ Exec into the pod and list environment variables  
You can use either of the following commands:

- Using `env`:
  ```sh
  kubectl exec -it envars -- env
  ```

- Using `printenv`:
  ```sh
  kubectl exec -it envars -- printenv
  ```

Both commands will list all environment variables inside the pod, including the ones set using `fieldRef`. 🚀

![image](https://github.com/user-attachments/assets/6a640302-0dcd-42e3-a973-8f6bd76e0fcb)

If your pod has multiple containers, specify the container name like this:  
```sh
kubectl exec -it envars -c fieldref-container -- printenv
```

---

### ℹ️ **Note**

Note: The kubectl utility on jump_host has been configured to work with the kubernetes cluster.

---
