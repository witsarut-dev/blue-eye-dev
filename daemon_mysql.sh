#!/bin/bash
ROOT_PATH="/opt/lampp/htdocs/blue-eye/index.php task"
PHP_PATH="/opt/lampp/bin/php/php5.6.20/bin/php"

KEY_PROCESS_1=$(ps -ef | grep "$ROOT_PATH task_keyword_match shell_exec_now 1" | grep -v grep | wc -l)


