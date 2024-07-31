# Game-Friend: University Teammate Matching Mini Program

## Project Introduction

Game-Friend is a mini program designed specifically for university students to help them find suitable teammates based on their major, degree, and achievements. It also provides a group chat feature for team communication. Additionally, the project supports backend data statistics for administrators to analyze and optimize services.

## Features Overview

### Main Features

1. **Teammate Recommendation**: Automatically recommends suitable teammates based on the user's major, degree, and achievements.
![13541722406273_ pic](https://github.com/user-attachments/assets/76fedbe4-8747-49ae-bc56-3379a42e9f5b)
2. **Group Chat**: Supports multi-user group chat for convenient team communication and collaboration.
![13591722408028_ pic](https://github.com/user-attachments/assets/4ac8a4a7-3f79-4d35-8f9a-6b80d989cb4d)
3. **Data Statistics**: Backend support for statistical analysis of user data and behavior to help optimize recommendation algorithms and user experience.
![13571722406299_ pic](https://github.com/user-attachments/assets/654182c0-19e0-4ae4-ac16-1e44d1fb78c0)


### Auxiliary Features

1. **User Registration and Login**: Users can register and log in using their student ID or email.
2. **User Profile Management**: Users can complete and update their personal profiles, including major, degree, and achievement information.
3. **Notification System**: The system can send match notifications and chat message reminders to users.

## Project Structure

- **backend**: Backend code, based on the Laravel framework, responsible for data storage, user management, recommendation algorithms, and statistical analysis.
- **frontend**: Frontend code, providing the user interface for registration, login, teammate recommendation, group chat, and more.
- **docs**: Project documentation, including development guides and API documentation.

## Technology Stack

- **Backend**: Laravel, MySQL
- **Frontend**: WeChat Mini Program
- **Data Statistics**: Python, Pandas
- **Deployment**: Docker, Amazon Web Services (AWS)

## Deployment and Running

### Prerequisites

- Docker
- Docker Compose
- AWS Account
- WeChat Developer Tools

### Installation Steps

1. **Clone the repository**:
    ```bash
    git clone https://github.com/your-username/game-friend.git
    cd game-friend
    ```

2. **Configure backend environment**:

    Create a `.env` file in the `backend` directory and configure MySQL database connection and other necessary environment variables.

3. **Build and start Docker containers**:
    ```bash
    docker-compose up -d
    ```

4. **Run database migrations and seeding**:
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

5. **Install frontend dependencies**:
    Open WeChat Developer Tools and import the `frontend` folder.

6. **Run the frontend mini program**:
    In WeChat Developer Tools, click "Preview" to run the mini program.

### Deploying to Amazon Web Services (AWS)

1. **Create an EC2 instance**:
    In the AWS console, create a new EC2 instance and configure the security group to allow HTTP, HTTPS, and SSH access.

2. **Connect to the EC2 instance**:
    Use SSH to connect to your EC2 instance:
    ```bash
    ssh -i your-key.pem ec2-user@your-ec2-instance.amazonaws.com
    ```

3. **Install Docker and Docker Compose**:
    ```bash
    sudo yum update -y
    sudo amazon-linux-extras install docker
    sudo service docker start
    sudo usermod -a -G docker ec2-user
    sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    ```

4. **Deploy the application**:
    Upload the project files to the EC2 instance and run the following command in the project root directory:
    ```bash
    docker-compose up -d
    ```

5. **Configure domain and SSL**:
    Configure your domain using AWS Route 53 and set up an SSL certificate using AWS Certificate Manager.

## Usage Instructions

### User Registration and Login

Users can register a new account using their student ID or email. Once logged in, they can access the teammate recommendation and group chat features.

### Teammate Recommendation

The system automatically recommends suitable teammates based on the user's major, degree, and achievements. Users can view the recommended list and send friend requests.

### Group Chat

Users can create or join group chats to communicate and collaborate with their teammates in real-time.

### Data Statistics

Administrators can view user behavior and data analysis reports through the backend data statistics feature to help optimize the system.

### API Documentation

Access the API documentation by visiting `http://your-ec2-instance.amazonaws.com:82` to view the Swagger UI.

## Contribution

We welcome any form of contribution! Please submit a Pull Request or contact the project maintainer.

## License

This project is licensed under the MIT License. For more details, please refer to the LICENSE file.

---

Thank you for using Game-Friend. We hope it helps with your studies and projects!

---

If you have any questions or suggestions, please feel free to provide feedback in the Issues section.
