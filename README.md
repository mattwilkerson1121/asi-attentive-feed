Attentive ASI product catalog upload Python script
1. Salsify creates a daily csv catalog file and saves to cloud
2. Scheduled CRON job runs on server and triggers a PHP file that formats the data to JSOND and saves the file
3. Then Python script runs that connects to the Attentive API and uploads the catalog file
