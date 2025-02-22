# Kubernetes Deployment Template for Web Application

## Task Statement  
The Nautilus DevOps team is tasked with deploying a web application on a Kubernetes cluster. The deployment requires the creation and configuration of Persistent Volumes (PV) and Persistent Volume Claims (PVC) to store the application code. Below are the detailed tasks to achieve this deployment.

---

## Task Details  

### **1. Create a PersistentVolume (PV)**  
Create a PersistentVolume named `pv-devops` with the following specifications:  
- **Storage Class**: `manual`  
- **Capacity**: `4Gi`  
- **Access Mode**: `ReadWriteOnce`  
- **Volume Type**: `hostPath`  
- **Path**: `/mnt/itadmin` (Note: This directory is pre-created and does not require direct access.)  

---

### **2. Create a PersistentVolumeClaim (PVC)**  
Create a PersistentVolumeClaim named `pvc-devops` with the following specifications:  
- **Storage Class**: `manual`  
- **Storage Request**: `1Gi`  
- **Access Mode**: `ReadWriteOnce`  

---

### **3. Create a Pod**  
Create a Pod named `pod-devops` with the following specifications:  
- **Container Name**: `container-devops`  
- **Image**: `nginx:latest` (Ensure the `latest` tag is explicitly mentioned.)  
- **Volume Mount**: Mount the PersistentVolumeClaim `pvc-devops` at the document root of the web server.  

---

### **4. Create a Service**  
Create a NodePort service named `web-devops` with the following specifications:  
- **Type**: `NodePort`  
- **Node Port**: `30008`  
- **Purpose**: Expose the web server running within the `pod-devops`.  

---

on the Kubernetes cluster.
