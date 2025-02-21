# 🚀 DevOps Default Ports Cheat Sheet  

## 🌐 Web & HTTP-Based Services
| Port  | Service           | Description                          |
|-------|------------------|--------------------------------------|
| **80**   | 🌎 HTTP           | Web Traffic                        |
| **443**  | 🔒 HTTPS          | Secure Web Traffic                 |
| **8080** | ⚙️ Jenkins/Tomcat | Alternate HTTP Port                |
| **8443** | 🔐 Kubernetes UI  | Alternate HTTPS Port               |

---

## 🔑 SSH, FTP & Remote Access
| Port  | Service         | Description                        |
|-------|---------------|------------------------------------|
| **22**   | 🔑 SSH         | Secure Shell (Remote Login)       |
| **21**   | 📂 FTP         | File Transfer Protocol            |
| **990**  | 🔐 FTPS        | FTP over SSL                      |
| **23**   | ⚠️ Telnet      | Unsecure Remote Access            |

---

## 🔄 CI/CD & DevOps Tools
| Port  | Service         | Description                          |
|-------|---------------|--------------------------------------|
| **8080** | ⚙️ Jenkins     | Web UI for Jenkins                  |
| **50000**| 🔗 Jenkins    | Agent Communication                 |
| **9000** | 🛠️ SonarQube   | Code Quality Analysis               |
| **9001** | 🎛️ SonarQube UI| SonarQube Web UI                    |

---

## 🐳 Docker & Kubernetes  
| Port      | Service             | Description                          |
|-----------|--------------------|--------------------------------------|
| **2375**  | 🐳 Docker Daemon    | Unencrypted Docker API               |
| **2376**  | 🔐 Docker Secure API| Encrypted Docker API                 |
| **5000**  | 🏭 Docker Registry  | Private Docker Registry              |
| **6443**  | 📡 K8s API Server   | Kubernetes API Server                |
| **10250** | ⚡ Kubelet API       | Node Management                      |
| **30000-32767** | 🔄 NodePort  | Kubernetes Exposed Services          |

---

## 💾 Databases & Storage
| Port  | Service        | Description                        |
|-------|--------------|------------------------------------|
| **3306**  | 🐬 MySQL      | MySQL Database                   |
| **5432**  | 🐘 PostgreSQL | PostgreSQL Database              |
| **27017** | 🍃 MongoDB    | MongoDB Database                 |
| **1433**  | 🏢 MSSQL      | Microsoft SQL Server             |
| **6379**  | 🔴 Redis      | In-Memory Data Store             |
| **9200**  | 🔍 Elasticsearch | Search & Analytics Engine   |

---

## 📊 Monitoring & Logging
| Port  | Service      | Description                          |
|-------|------------|--------------------------------------|
| **9090**  | 📡 Prometheus | Monitoring Tool                   |
| **3000**  | 📊 Grafana    | Dashboard & Visualization         |
| **5601**  | 📜 Kibana     | Log Analysis (ELK Stack)         |
| **1514**  | 📝 Syslog     | Logging Service                   |

---

## 🏗️ Load Balancers & Networking
| Port   | Service       | Description                     |
|--------|-------------|---------------------------------|
| **53**    | 🌍 DNS        | Domain Name System            |
| **67/68** | 🔄 DHCP       | Dynamic Host Configuration    |
| **443/80**| 🔁 Proxy      | HAProxy / Nginx / Apache      |
| **500/4500** | 🔐 IPSec VPN | Secure VPN Connections |

---

## 📡 Message Brokers & Streaming
| Port   | Service      | Description                   |
|--------|------------|------------------------------|
| **9092**  | 🚀 Kafka      | Distributed Event Streaming  |
| **5672**  | 📨 RabbitMQ   | Message Broker               |
| **1883**  | 📡 MQTT       | IoT Messaging Protocol       |

---

## 🔐 Security & Authentication
| Port   | Service      | Description                   |
|--------|------------|------------------------------|
| **636**   | 🔒 LDAPS      | LDAP over SSL                 |
| **389**   | 🏛️ LDAP       | Lightweight Directory Access   |
| **1812**  | 🛡️ RADIUS     | Authentication Protocol       |

---

💡 **Note:** These are default ports; they can be customized in configurations.  
🚀 Stay Secure & Automate Efficiently!  
