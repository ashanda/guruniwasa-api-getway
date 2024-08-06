pipeline {
    agent any

    environment {
        SSH_PRIVATE_KEY = credentials('GEEKMAC')  // Ensure this is the correct ID for your SSH private key credentials
    }

    stages {
        stage('Checkout code') {
            steps {
                checkout([$class: 'GitSCM', branches: [[name: '*/dev']], userRemoteConfigs: [[url: 'https://github.com/Geek-Mac-Solutions/students-panel.git']]])  // Change URL to your repo
            }
        }

        stage('Install SSH key') {
            steps {
                script {
                    // Use ssh-agent to handle the SSH key
                    sshagent(['GEEKMAC']) {
                        sh 'ssh-add -l'  // List loaded SSH keys to verify
                    }
                }
            }
        }

        stage('Deploy website via SSH') {
            steps {
                script {
                    sshagent(['GEEKMAC']) {
                        // Deploy files via scp and create timestamp file via ssh
                        sh '''
                            scp -o StrictHostKeyChecking=no -r ./* root@52.221.10.55:/www/wwwroot/student.work.gd
                            ssh -o StrictHostKeyChecking=no root@52.221.10.55 "touch /www/wwwroot/student.work.gd/$(date +\"%Y-%m-%d_%H-%M-%S\").txt"
                        '''
                    }
                }
            }
        }
    }

    post {
        cleanup {
            // Remove the SSH key file if necessary//
            sh 'rm -f id_rsa'
        }
    }
}
