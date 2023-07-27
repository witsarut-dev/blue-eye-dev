#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php"

#task_link_match
KEY_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_link_match run_match_link 1" | grep -v grep | wc -l)
if [ $KEY_PROCESS_1 -gt 0 ]; then
	echo "Process task_link_match is running."
else
	$PHP_PATH $ROOT_PATH task_link_match run_match_link 1
fi
