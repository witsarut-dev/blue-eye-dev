#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php"

# task_keyword_match
KEY_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_keyword_match run_time 1" | grep -v grep | wc -l)
if [ $KEY_PROCESS_1 -gt 0 ]; then
  	echo "Process task_keyword_match 1 is running."
else
	$PHP_PATH $ROOT_PATH task_keyword_match run_time 1
fi

KEY_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_keyword_match run_time 2" | grep -v grep | wc -l)
if [ $KEY_PROCESS_2 -gt 0 ]; then
  	echo "Process task_keyword_match 2 is running."
else
	$PHP_PATH $ROOT_PATH task_keyword_match run_time 2
fi

# task_category_match
CATE_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_category_match run_time 1" | grep -v grep | wc -l)
if [ $CATE_PROCESS_1 -gt 0 ]; then
  	echo "Process task_category_match 1 is running."
else
	$PHP_PATH $ROOT_PATH task_category_match run_time 1
fi

CATE_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_category_match run_time 2" | grep -v grep | wc -l)
if [ $CATE_PROCESS_2 -gt 0 ]; then
  	echo "Process task_category_match 2 is running."
else
	$PHP_PATH $ROOT_PATH task_category_match run_time 2
fi

## task_sentiment
# SENT_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_sentiment run_time 1" | grep -v grep | wc -l)
# if [ $SENT_PROCESS_1 -gt 0 ]; then
#         echo "Process task_sentiment 1 is running."
# else
#         $PHP_PATH $ROOT_PATH task_sentiment run_time 1
# fi

# SENT_PROCESS_2=$(ps -ef | grep "$ROOT_PATH task_sentiment run_time 2" | grep -v grep | wc -l)
# if [ $SENT_PROCESS_2 -gt 0 ]; then
#         echo "Process task_sentiment 2 is running."
# else
#         $PHP_PATH $ROOT_PATH task_sentiment run_time 2
# fi

# CLEAR_PROCESS=$(ps -ef | grep "$ROOT_PATH task_sentiment clear_keyword" | grep -v grep | wc -l)
# if [ $CLEAR_PROCESS -gt 0 ]; then
#         echo "Process clear_keyword is running."
# else
#         $PHP_PATH $ROOT_PATH task_sentiment clear_keyword
# fi
