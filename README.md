
# AI Bill Detection Setup

This repository contains instructions for setting up the **AI Bill Detection** system, including an AI server, OAuth 2.0 server, and demo phone application.

## Table of Contents
1. [AI Environment Installation](#ai-environment-installation)
2. [AI Server Setup](#ai-server-setup)
3. [OAuth 2.0 Server Setup](#oauth-20-server-setup)
4. [Demo Phone App](#demo-phone-app)

---

## AI Environment Installation

### Requirements
1. **Anaconda**: Install version 23.7.4  
   [Download here](https://anaconda.org/conda-forge/conda/files?sort=time&sort_order=desc&version=23.7.4)
2. **Python**: Install version 3.11.5.

### Python Dependencies
Activate the Anaconda environment and run:
```bash
pip install easyocr
pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cu118
pip install numpy
pip install cv2
pip install imutils
pip install screeninfo
```

### GPU Setup (Optional)
- **NVIDIA RTX 3060**:
  ```bash
  pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cu118
  ```
- **Other GPUs**:
  1. Check GPU information:
     ```bash
     nvidia-smi
     nvcc --version
     ```
  2. Install required drivers and tools:
     - [NVIDIA Driver](https://www.nvidia.com/en-us/drivers/)
     - [CUDA Toolkit](https://developer.nvidia.com/cuda-toolkit)
     - [cuDNN](https://developer.nvidia.com/cudnn)

---

## AI Server Setup

### Requirements
Install Apache 2.4, PHP, and MySQL.

### API Endpoints
- **Register User**: `register.php`
- **User Login**: `login/login.php`
- **Token Validation**: `login/check_login.php`
- **Bill Analysis**: `take_photo.php` (uploads image and calls Python API)

Logs are stored in: `logs/log.txt`.

---

## OAuth 2.0 Server Setup

### Database Setup
The OAuth 2.0 server requires two tables:
1. **Client Table**: Contains information about registered client apps.
   - Fields: `client_id`, `client_secret`, `redirect_url`.
2. **User Table**: Contains user data for authentication.

### API Components
- **Client API**:
  - Authorization: `authorize.php`
  - Access Token: `token.php`
  - Resource Access: `resource_owner.php`
- **Authentication API**:
  - Handles user login and resource authorization.
- **Resource Server API**:
  - Parses access tokens and provides protected resources.

### Keys and Configuration
1. Generate public and private keys:
   [Key Generation Guide](https://oauth2.thephpleague.com/installation/)
2. Update `.env` files in relevant API folders.

---

## Demo Phone App

The demo application, built with Unity 2022.3.20f1, supports Android and iOS (currently optimized for Android).

### Usage
- Pre-registered credentials:
  - Username: `admin@gmail.com`
  - Password: `123456`
- Features:
  - Login and authenticate via the OAuth 2.0 server.
  - Upload and analyze bills using the AI API.

---

For any questions or issues, please contact: [hydragroup.info@gmail.com](mailto:hydragroup.info@gmail.com)
