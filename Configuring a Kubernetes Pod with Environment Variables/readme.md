

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

---

## 🔄 **Restart Policy**

Set restart policy to Never.

---

## ✅ **Verification**

To check the output, exec into the pod and use printenv command.

---

### ℹ️ **Note**

Note: The kubectl utility on jump_host has been configured to work with the kubernetes cluster.

---
