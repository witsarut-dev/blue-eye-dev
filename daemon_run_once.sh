#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php"

# task_clear_data
CLEAR_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_once_data run_once" | grep -v grep | wc -l)
if [ $CLEAR_PROCESS_1 -gt 0 ]; then
	echo "Process task_once_data is running."
else
	$PHP_PATH $ROOT_PATH task_once_data run_once
fi

# task_map_one
# CLEAR_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_once_data run_once" | grep -v grep | wc -l)
# if [ $CLEAR_PROCESS_1 -gt 0 ]; then
# 	echo "Process task_once_data is running."
# else
# 	$PHP_PATH $ROOT_PATH task_once_data run_once
# fi

#MAP_RUN_ONCE
# MAP_PROCESS_ONCE=$(ps -ef | grep "$ROOT_PATH task_map_match run_map_match 1" | grep -v grep | wc -l)
# if [ $MAP_PROCESS_ONCE -gt 0 ]; then
# 	echo "Process task_map_match once is running."
# else
# 	$PHP_PATH $ROOT_PATH task_map_match run_map_match 1
# fi
