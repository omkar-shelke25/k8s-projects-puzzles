

# **🔹 Understanding Redis Configuration in Depth**
Redis configurations control how the database operates, including **memory management, persistence, security, networking, and replication**.

### **📌 Where to Define Redis Configuration?**
There are **three** main ways to configure Redis:

### **1️⃣ Using the `redis.conf` File (Recommended for Permanent Settings)**
- The primary Redis configuration file is **`redis.conf`**.
- Found at:
  - **Linux/macOS**: `/etc/redis/redis.conf`
  - **Windows**: `redis.windows.conf`
- You can modify settings and restart Redis to apply them.
- Example:
  ```ini
  port 6379
  requirepass "MySecurePassword"
  maxmemory 512mb
  ```

### **2️⃣ Using `CONFIG SET` Command (Temporary Runtime Changes)**
- Used to change settings **without restarting Redis**.
- Changes are **not persistent** (lost after restart).
- Example:
  ```sh
  redis-cli CONFIG SET maxmemory 256mb
  redis-cli CONFIG SET requirepass "NewPassword123"
  ```

### **3️⃣ Using Command-Line Arguments (For One-Time Execution)**
- Used when **starting Redis manually**.
- Example:
  ```sh
  redis-server --port 6380 --requirepass "MySecurePass"
  ```
- **Drawback**: Settings are lost once Redis stops.

---

# **📝 Complete List of Redis Configuration Directives**
Below is a **detailed list** of **all Redis configuration directives**, categorized by functionality.

---

## **⚙️ 1. General Settings (Basic Redis Behavior)**
These settings define how Redis runs.

| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `daemonize` | Run Redis as a background process (`yes`/`no`). | `daemonize yes` |
| `pidfile` | Location of PID file when daemonized. | `pidfile /var/run/redis.pid` |
| `port` | Port number Redis listens on (default: `6379`). | `port 6379` |
| `bind` | IP address Redis binds to. | `bind 127.0.0.1` |
| `protected-mode` | Prevents access unless explicitly bound (`yes`/`no`). | `protected-mode yes` |
| `timeout` | Disconnect clients after inactivity (seconds). | `timeout 300` |
| `tcp-keepalive` | Interval for TCP keepalive messages. | `tcp-keepalive 60` |

✅ **Best Practice:** **Always use `protected-mode yes`** to prevent unauthorized access.

---

## **💾 2. Memory Management**
These settings control **Redis's memory usage and eviction policy**.

| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `maxmemory` | Maximum RAM Redis can use. | `maxmemory 1gb` |
| `maxmemory-policy` | Eviction strategy when memory is full. | `maxmemory-policy allkeys-lru` |
| `maxmemory-samples` | Number of keys checked for eviction. | `maxmemory-samples 5` |

### **🚀 Eviction Policies in `maxmemory-policy`:**
- `noeviction` → Don't remove anything, return an error when memory is full.
- `allkeys-lru` → Remove least recently used (LRU) keys.
- `volatile-lru` → Remove LRU keys **only with TTL**.
- `allkeys-random` → Remove random keys.
- `volatile-random` → Remove random keys **only with TTL**.
- `volatile-ttl` → Remove the shortest-lived keys.

✅ **Best Practice:** Use `allkeys-lru` for caching scenarios.

---

## **📜 3. Persistence (Data Storage & Recovery)**
Redis offers **two persistence mechanisms**:
1. **RDB (Redis Database Backup)**
2. **AOF (Append-Only File for durability)**

### **🔹 RDB Persistence (Snapshotting)**
| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `save` | Set automatic snapshot intervals. | `save 900 1` (Every 900 sec, if 1 key changes) |
| `stop-writes-on-bgsave-error` | Stop writing if snapshot fails. | `stop-writes-on-bgsave-error yes` |
| `rdbcompression` | Compress RDB files. | `rdbcompression yes` |
| `rdbchecksum` | Verify RDB file integrity. | `rdbchecksum yes` |

✅ **Best Practice:** **Use `save 900 1 300 10 60 10000`** for frequent backups.

---

### **🔹 AOF Persistence (Durability)**
| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `appendonly` | Enable AOF persistence. | `appendonly yes` |
| `appendfsync` | AOF sync strategy. | `appendfsync everysec` |

### **🛠️ `appendfsync` Options:**
- `always` → Writes **every operation** (slow but safe).
- `everysec` → Writes **once per second** (**best option**).
- `no` → Let the OS decide when to write (fastest but risky).

✅ **Best Practice:** Use **AOF (`appendonly yes`) with `appendfsync everysec`** for high durability.

---

## **🔁 4. Replication (Master-Slave Setup)**
Redis supports **master-slave replication** for **high availability**.

| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `replicaof` | Sets Redis as a slave to a master. | `replicaof 192.168.1.100 6379` |
| `masterauth` | Password for authenticating with master. | `masterauth mypassword` |

✅ **Best Practice:** Use **replication** for high availability in production.

---

## **🔐 5. Security & Access Control**
| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `requirepass` | Set a password for clients. | `requirepass MySecurePass` |
| `rename-command` | Disable or rename dangerous commands. | `rename-command FLUSHALL ""` |

✅ **Best Practice:** **Always set a strong `requirepass`** in production.

---

## **📊 6. Logging & Monitoring**
| **Directive** | **Purpose** | **Example** |
|--------------|------------|------------|
| `loglevel` | Set log verbosity. | `loglevel notice` |
| `logfile` | File to store logs. | `logfile "/var/log/redis.log"` |

✅ **Best Practice:** Use `loglevel notice` for balanced logging.

---

# **🚀 How to Apply Changes?**
1. **Edit `redis.conf` and Restart Redis:**
   ```sh
   sudo nano /etc/redis/redis.conf
   sudo systemctl restart redis
   ```
2. **Apply Changes Dynamically (Temporary)**
   ```sh
   redis-cli CONFIG SET maxmemory 512mb
   ```

---

# **🎯 Final Recommendations**
✅ **Use `redis.conf` for permanent settings**  
✅ **Use `CONFIG SET` for temporary changes**  
✅ **Enable authentication (`requirepass`) in production**  
✅ **Use `maxmemory` and `allkeys-lru` for caching**  
✅ **Use AOF (`appendonly yes`) for durability**

---

