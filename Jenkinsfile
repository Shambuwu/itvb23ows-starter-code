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

        stage('SonarQube Analysis') {
            steps {
                script {
                    scannerHome = tool 'SonarQube'
                }

                withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner"
                }

                echo 'SonarQube analysis completed'
            }
        }
    }
}
