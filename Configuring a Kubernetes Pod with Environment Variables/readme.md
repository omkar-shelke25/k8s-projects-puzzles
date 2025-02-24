

# 📋 **Problem Statement: Configuring a Kubernetes Pod with Environment Variables**

We need to define several parameters as environment variables to be used across different configurations in a Kubernetes cluster. Below is the scenario to be implemented.

---

## 🛠️ **Task: Create a Kubernetes Pod**

- **Pod Name**: `envars`  
- **Container Name**: `fieldref-container`  
- **Image**: `redis` (latest tag preferred)  
- **Command**: `sh`, `-c`  
- **Args**:  
  ```bash
  while true; do
      echo -en '\n';
      printenv NODE_NAME POD_NAME;
      printenv POD_IP POD_SERVICE_ACCOUNT;
      sleep 10;
  done;
  ```  
  *(Note: Ensure proper indentation in the YAML file)*

---

## 🌍 **Environment Variables Configuration**

Define the following four environment variables using `valueFrom` and `fieldRef`:

1. **🔧 NODE_NAME**  
   - Name: `NODE_NAME`  
   - Value Source: `fieldRef`  
   - Field Path: `spec.nodeName`

2. **📛 POD_NAME**  
   - Name: `POD_NAME`  
   - Value Source: `fieldRef`  
   - Field Path: `metadata.name`

3. **🌐 POD_IP**  
   - Name: `POD_IP`  
   - Value Source: `fieldRef`  
   - Field Path: `status.podIP`

4. **🔑 POD_SERVICE_ACCOUNT**  
   - Name: `POD_SERVICE_ACCOUNT`  
   - Value Source: `fieldRef`  
   - Field Path: `spec.serviceAccountName`

---

## 🔄 **Restart Policy**

- **Policy**: `Never`

---

## ✅ **Verification Steps**

- Exec into the pod:  
  `kubectl exec -it envars -- sh`  
- Run:  
  `printenv`  
- Check the output of the environment variables.

---

## ℹ️ **Additional Notes**

- The `kubectl` utility on the `jump_host` is already configured to work with the Kubernetes cluster.

