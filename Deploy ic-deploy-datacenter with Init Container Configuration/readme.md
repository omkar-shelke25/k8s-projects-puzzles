**Task Title: Deploy ic-deploy-datacenter with Init Container Configuration**

**Problem Statement:**  
Applications deployed on a Kubernetes cluster require pre-deployment configuration changes not possible within their images. The DevOps team will use init containers to handle these changes. Test this with a Deployment named `ic-deploy-datacenter`:  
- 1 replica, labels `app: ic-datacenter` (spec and template metadata).  
- Init container: `ic-msg-datacenter`, image `centos:latest`, command `'/bin/bash', '-c', 'echo Init Done - Welcome to xFusionCorp Industries > /ic/news'`, volume mount `ic-volume-datacenter` at `/ic`.  
- Main container: `ic-main-datacenter`, image `centos:latest`, command `'/bin/bash', '-c', 'while true; do cat /ic/news; sleep 5; done'`, volume mount `ic-volume-datacenter` at `/ic`.  
- Volume: `ic-volume-datacenter`, type `emptyDir`.
