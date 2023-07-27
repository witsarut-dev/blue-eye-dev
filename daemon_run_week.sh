#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php"
#PHP_PATH="/opt/lampp/bin/php/php5.6.30/bin/php"
# task_week_data
WEEK_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_week_data run_week" | grep -v grep | wc -l)
if [ $WEEK_PROCESS_1 -gt 0 ]; then
	echo "Process task_week_data is running."
else
	$PHP_PATH $ROOT_PATH task_week_data run_week
fi
