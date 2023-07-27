#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php"

# task_keyword_match
# KEY_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_keyword_match run_now 1" | grep -v grep | wc -l)
# if [ $KEY_PROCESS_1 -gt 0 ]; then
#  	echo "Process task_keyword_match 1 is running."
# else
# 	$PHP_PATH $ROOT_PATH task_keyword_match run_now 1
# fi

# KEY_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_keyword_match run_now 2" | grep -v grep | wc -l)
# if [ $KEY_PROCESS_2 -gt 0 ]; then
#  	echo "Process task_keyword_match 2 is running."
# else
# 	$PHP_PATH $ROOT_PATH task_keyword_match run_now 2
# fi

KEY_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_keyword_match shell_exec_now 1" | grep -v grep | wc -l)
if [ $KEY_PROCESS_1 -gt 0 ]; then
	echo "Process task_keyword_match 1 is running."
else
	$PHP_PATH $ROOT_PATH task_keyword_match shell_exec_now 1
fi

KEY_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_keyword_match shell_exec_now 2" | grep -v grep | wc -l)
if [ $KEY_PROCESS_2 -gt 0 ]; then
	echo "Process task_keyword_match 2 is running."
else
	$PHP_PATH $ROOT_PATH task_keyword_match shell_exec_now 2
fi

#task_category_match
#CATE_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_category_match run_now 1" | grep -v grep | wc -l)
#if [ $CATE_PROCESS_1 -gt 0 ]; then
#  	echo "Process task_category_match 1 is running."
#else
#	$PHP_PATH $ROOT_PATH task_category_match run_now 1
#fi

#CATE_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_category_match run_now 2" | grep -v grep | wc -l)
#if [ $CATE_PROCESS_2 -gt 0 ]; then
#  	echo "Process task_category_match 2 is running."
#else
#	$PHP_PATH $ROOT_PATH task_category_match run_now 2
#fi

CATE_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_category_match shell_exec_now 1" | grep -v grep | wc -l)
if [ $CATE_PROCESS_1 -gt 0 ]; then
	echo "Process task_category_match 1 is running."
else
	$PHP_PATH $ROOT_PATH task_category_match shell_exec_now 1
fi

CATE_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_category_match shell_exec_now 2" | grep -v grep | wc -l)
if [ $CATE_PROCESS_2 -gt 0 ]; then
	echo "Process task_category_match 2 is running."
else
	$PHP_PATH $ROOT_PATH task_category_match shell_exec_now 2
fi

#Map_match task
# MAP_PROCESS=$(ps -ef | grep "$ROOT_PATH task_map_match run_map_match" | grep -v grep | wc -l)
# if [ $MAP_PROCESS -gt 0 ]; then
# 	echo "Process task_map_match is running."
# else
# 	$PHP_PATH $ROOT_PATH task_map_match run_map_match
# fi

#Timeline task
TIMELINE_PROCESS=$(ps -ef | grep "$ROOT_PATH task_timeline run_timeline" | grep -v grep | wc -l)
if [ $TIMELINE_PROCESS -gt 0 ]; then
echo "Process task_timeline is running."
else
$PHP_PATH $ROOT_PATH task_timeline run_timeline
fi
