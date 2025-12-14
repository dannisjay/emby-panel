# Emby Panel
---
### 🖼️ 面板总览

**核心界面**  
<img src="https://raw.githubusercontent.com/dannisjay/emby_panel/refs/heads/main/images/%E6%B3%A8%E5%86%8C%E9%A1%B5%E9%9D%A2.png" alt="注册页面" width="48%" />
<img src="https://raw.githubusercontent.com/dannisjay/emby_panel/refs/heads/main/images/%E7%AE%A1%E7%90%86%E7%95%8C%E9%9D%A2.png" alt="管理主界面" width="48%" />

**功能管理**  
<img src="https://raw.githubusercontent.com/dannisjay/emby_panel/refs/heads/main/images/%E7%94%A8%E6%88%B7%E7%AE%A1%E7%90%86.png" alt="用户管理" width="32%" />
<img src="https://raw.githubusercontent.com/dannisjay/emby_panel/refs/heads/main/images/%E9%82%80%E8%AF%B7%E7%A0%81.png" alt="邀请码管理" width="32%" />
<img src="https://raw.githubusercontent.com/dannisjay/emby_panel/refs/heads/main/images/%E5%AA%92%E4%BD%93%E5%BA%93%E7%AE%A1%E7%90%86.png" alt="媒体库管理" width="32%" />



## 部署教程

### 1. docker-compose.yml
```bash
services:
  emby-panel:
    image: dannis1514/emby-panel:latest
    container_name: emby-panel
    ports:
      - "8080:80"
    volumes:
      - ./config.php:/var/www/html/config.php:ro
      - ./data:/data
      - ./logs:/logs
    environment:
      TZ: Asia/Shanghai
    restart: unless-stopped
```
### 2. config.php

### 3. 访问面板
#### 浏览器打开下面地址
```bash
http://你的服务器IP:8080
```
