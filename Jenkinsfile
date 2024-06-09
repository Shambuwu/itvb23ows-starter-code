pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                echo 'Building Docker containers...'
                sh 'php -v'
            }
        }

        stage('Run Tests') {
                    steps {
                        echo 'Running PHPUnit tests...'
                        sh './vendor/bin/phpunit'
                    }
                }

        stage('SonarQube Analysis') {
            steps {
                script {
                    scannerHome = tool 'SonarQube Scanner'
                }

                withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner"
                }

                echo 'SonarQube analysis completed'
            }
        }
    }
}
