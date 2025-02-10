<?php
cli_set_process_title('repomanager.task-run');

define('ROOT', '/var/www/repomanager');
require_once(ROOT . "/controllers/Autoloader.php");
new \Controllers\Autoloader('api');

ini_set('memory_limit', TASK_EXECUTION_MEMORY_LIMIT . 'M');

$myTask = new \Controllers\Task\Task();
$mylog = new \Controllers\Log\Log();
$myFatalErrorHandler = new \Controllers\FatalErrorHandler();

/**
 *  Getting options from command line: task Id is required and cannot be empty.
 *
 *  First parameter passed to getopt is null: we don't want to work with short options.
 *  More infos about getopt() : https://blog.pascal-martin.fr/post/php-5.3-getopt-parametres-ligne-de-commande/
 */
$getOptions = getopt(null, ["id:"]);

try {
    /**
     *  Retrieve task Id
     */
    if (empty($getOptions['id'])) {
        throw new Exception('Task Id is not defined');
    }

    $taskId = $getOptions['id'];

    /**
     *  Set task Id for fatal error handler
     */
    $myFatalErrorHandler->setTaskId($taskId);

    /**
     *  Retrieve task details
     */
    $taskParams = $myTask->getById($taskId);

    if (empty($taskParams)) {
        throw new Exception('Cannot get task details from task #' . $taskId . ': empty results.');
    }

    $taskParams = json_decode($taskParams['Raw_params'], true);

    if (empty($taskParams['action'])) {
        throw new Exception('Action not specified');
    }

    /**
     *  Generate controller name
     */
    $controllerPath = '\Controllers\Task\Repo\\' . ucfirst($taskParams['action']);

    /**
     *  Check if class exists, otherwise the action might be invalid
     */
    if (!class_exists($controllerPath)) {
        throw new Exception('Invalid action: ' . $taskParams['action']);
    }

    /**
     *  If task queuing is enabled and the maximum number of simultaneous tasks is set, check if the task can be started
     */
    if (!empty(TASK_QUEUING) and TASK_QUEUING == 'true' and !empty(TASK_QUEUING_MAX_SIMULTANEOUS)) {
        while (true) {
            /**
             *  Get running tasks
             */
            $runningTasks = $myTask->listRunning();

            /**
             *  If number of running tasks is greater than or equal to the maximum number of simultaneous tasks, we wait
             */
            if (count($runningTasks) >= TASK_QUEUING_MAX_SIMULTANEOUS) {
                sleep(5);
                continue;
            }

            /**
             *  Get the newest task in the queue (tasks with Status = 'new')
             */
            $newestTask = $myTask->listNewest();

            /**
             *  If the first task in the newest tasks list has the same Id as $taskId, then this task can be started
             */
            if ($newestTask[0]['Id'] == $taskId) {
                break;
            }

            // Just for safety
            sleep(5);
        }
    }

    /**
     *  Instantiate controller and execute action
     */
    $controller = new $controllerPath($taskId);
    $controller->execute();

/**
 *  Catch exceptions
 */
} catch (Exception $e) {
    $mylog->log('error', 'An exception error occured while running task #' . $taskId, $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . PHP_EOL;
    exit(1);

/**
 *  Catch fatal errors
 */
} catch (Error $e) {
    $mylog->log('error', 'A fatal error occured while running task #' . $taskId, $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo 'Fatal error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . PHP_EOL;
    exit(1);
}

exit(0);
