# Problem Statement: Deploying a Redis Cluster with Kubernetes StatefulSet

## Objective

Design and deploy a highly available Redis cluster on Kubernetes using a StatefulSet to ensure stable, scalable, and persistent data storage for a distributed caching system. The cluster must support 6 replicas, each running Redis 5.0.1, with persistent storage and proper network configurations for client and gossip communications.

## Diagram

The architecture is illustrated in the following diagram:

The diagram depicts the StatefulSet (`redis-cluster`), 6 pods (`redis-cluster-0` to `redis-cluster-5`), PersistentVolumes (`redis01` to `redis06`), ConfigMap (`redis-cluster-configmap`), and the service (`redis-cluster-service`) exposing client and gossip ports.

## Requirements

### StatefulSet Configuration

- **Name**: `redis-cluster`
- **Replicas**: 6
- **Pod Status**: All 6 replicas must be in `Running` state
- **Pod Labels**: `app: redis-cluster`
- **Container Details**:
  - **Image**: `redis:5.0.1-alpine`
  - **Container Name**: `redis`
  - **Command**: `["/conf/update-node.sh", "redis-server", "/conf/redis.conf"]`
  - **Environment Variables**:
    - Name: `POD_IP`
    - Value Source: `fieldRef` with `fieldPath: status.podIP`
  - **Ports**:
    - Name: `client`, ContainerPort: `6379`
    - Name: `gossip`, ContainerPort: `16379`
  - **Volume Mounts**:
    - Name: `conf`, MountPath: `/conf`, ReadOnly: `false` (ConfigMap-backed)
    - Name: `data`, MountPath: `/data`, ReadOnly: `false` (PersistentVolumeClaim-backed)

### Volumes

- **ConfigMap Volume**:
  - Name: `conf`
  - Type: `ConfigMap`
  - ConfigMap Name: `redis-cluster-configmap`
  - Default Mode: `0755`
- **PersistentVolumeClaim Template**:
  - Name: `data`
  - Access Mode: `ReadWriteOnce`
  - Storage Request: `1Gi`

### Persistent Volumes

- **Names**: `redis01`, `redis02`, `redis03`, `redis04`, `redis05`, `redis06`
- **Access Mode**: `ReadWriteOnce`
- **Size**: `1Gi` each
- **HostPath**:
  - `redis01`: `/redis01`
  - `redis02`: `/redis02`
  - `redis03`: `/redis03`
  - `redis04`: `/redis04`
  - `redis05`: `/redis05`
  - `redis06`: `/redis06`
- **Directory Creation**: Ensure each directory is created on the worker node.

### Service Configuration

- **Service Name**: `redis-cluster-service`
- **Ports**:
  - Name: `client`, Port: `6379`, TargetPort: `6379`
  - Name: `gossip`, Port: `16379`, TargetPort: `16379`

### Cluster Initialization

- **Command**:

  ```bash
  kubectl exec -it redis-cluster-0 -- redis-cli --cluster create --cluster-replicas 1 $(kubectl get pods -l app=redis-cluster -o jsonpath='{range.items[*]}{.status.podIP}:6379 {end}')
  ```

- **Purpose**: Initialize the Redis cluster with one replica per primary node (3 primary, 3 replica nodes).

## Diagram Details

The diagram illustrates:

- **StatefulSet**: Manages 6 pods (`redis-cluster-0` to `redis-cluster-5`) with stable identities.
- **Pods**: Each contains a Redis container with ports `6379` (client) and `16379` (gossip).
- **Persistent Storage**: Each pod is linked to a dedicated PersistentVolume (`redis01` to `redis06`) via a PVC, ensuring data persistence.
- **ConfigMap**: `redis-cluster-configmap` provides configuration files to all pods, mounted at `/conf`.
- **Service**: `redis-cluster-service` exposes ports `6379` and `16379` for external client access and internal cluster communication.
- **Network Flow**: Pods communicate internally via gossip protocol (`16379`) and serve clients via the service (`6379`).

## Challenges

1. **Persistence**: Ensure each pod has a dedicated PersistentVolume with the correct hostPath directory created on the worker node.
2. **Networking**: Guarantee stable pod IP assignments and proper exposure of client (`6379`) and gossip (`16379`) ports via the service.
3. **Cluster Formation**: Successfully execute the `redis-cli --cluster create` command to form a cluster with 3 primary and 3 replica nodes.
4. **Configuration Management**: Correctly mount the ConfigMap to provide Redis configuration files with appropriate permissions.
5. **HostPath Scalability**: The use of hostPaths (`/redis01` to `/redis06`) may limit scalability in multi-node clusters and requires manual directory setup.

## Success Criteria

- All 6 Redis pods are running and accessible.
- PersistentVolumes are correctly bound to their respective pods with no data loss.
- The Redis cluster is initialized with 3 primary nodes and 3 replicas, verified via `redis-cli cluster info`.
- The `redis-cluster-service` exposes ports `6379` and `16379` for client and gossip communications.
- The system handles pod failures gracefully, maintaining data persistence and cluster integrity.
