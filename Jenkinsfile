pipeline {
    agent { dockerfile true }

    stages {
        stage('Build') {
            steps {
                echo 'Building Docker containers...'
                sh 'php -v'
            }
        }

        stage('Test Web') {
            steps {
                echo 'Running tests in the Web container...'
            }
        }

        stage('SonarQube analysis') {
            steps {
                withSonarQubeEnv('SonarQube') {
                    sh 'sonar-scanner'
                }
            }
        }
    }
}
