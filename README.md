## 🛠️ Cara install di projek

### 🔹 **Step 1: Clone the Repository**

```sh
git clone https://github.com/akaz-cyber/Project---Jirifarm-Team-UTB
```

### 🔹 **Step 2: Install Dependencies**

```sh
composer install
npm install
```

### 🔹 **Step 3: Environment Setup**

```sh
cp .env.example .env
php artisan key:generate
```

Perbarui `.env` dengan kredensial database anda.

### 🔹 **Step 4: Database Configuration**

```sh
php artisan migrate --seed
```

### 🔹 **Step 5: Setup Storage**

```sh
php artisan storage:link
```

### 🔹 **Step 6: Run the Application**

```sh
php artisan serve
```

🔗 Open `http://localhost:8000`

### **Admin Login Credentials:**

📧 **Email:** `admin@gmail.com`  
🔑 **Password:** `AdminJiriF4RM!`

### ** SUPER Admin Login Credentials:**

📧 **Email:** `superadmin@gmail.com`  
🔑 **Password:** `SU4Dmin!25`

### ** User Login Credentials:**

📧 **Email:** `user@gmail.com`  
🔑 **Password:** `user12345`
