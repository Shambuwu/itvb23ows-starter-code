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
                // Vervang 'your_test_script.sh' met je daadwerkelijke test commando of script
                // sh 'docker-compose run web your_test_script.sh'
            }
        }
    }
}
