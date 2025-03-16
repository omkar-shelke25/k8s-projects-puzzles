
**Problem Statement: Deploying a Drupal Application on Kubernetes Cluster**

The Nautilus application development team requires a Kubernetes-based deployment for a fresh Drupal application, which they will install manually. The setup must meet the following specifications:

1. **Persistent Volume Configuration**:  
   Create a Persistent Volume named `drupal-mysql-pv` with:  
   - `hostPath` set to `/drupal-mysql-data` (pre-existing directory on the worker node/jump host).  
   - Storage capacity of 5Gi.  
   - Access mode set to `ReadWriteOnce`.

2. **Persistent Volume Claim Configuration**:  
   Create a PersistentVolumeClaim named `drupal-mysql-pvc` with:  
   - Storage request of 3Gi.  
   - Access mode set to `ReadWriteOnce`.

3. **MySQL Deployment**:  
   Deploy a Kubernetes Deployment named `drupal-mysql` with:  
   - 1 replica.  
   - Image: `mysql:5.7`.  
   - Mount the `drupal-mysql-pvc` at `/var/lib/mysql`.

4. **Drupal Deployment**:  
   Deploy a Kubernetes Deployment named `drupal` with:  
   - 1 replica.  
   - Image: `drupal:8.6`.

5. **Drupal Service**:  
   Create a `NodePort` Service named `drupal-service` with:  
   - NodePort set to `30095`.  
   - Exposing the Drupal deployment for external access.

6. **MySQL Service**:  
   Create a Service named `drupal-mysql-service` to:  
   - Expose the `drupal-mysql` deployment on port `3306`.

7. **Additional Configuration**:  
   Configure any necessary settings (e.g., environment variables, secrets) for the deployments and services to ensure compatibility and functionality. The final setup should allow access to the Drupal installation page via the "App" button.

