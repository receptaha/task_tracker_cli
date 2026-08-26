<?php

declare(strict_types=1);
require_once "constants.php";

function handle_query(int $argc, array $argv): void
{
    if($argc <= 1) {
        throw new \InvalidArgumentException("Arguments are required");
    }
    $command = $argv[1];
    check_command($command);
    check_arguments_for($command);
    handle_command($command);
}

function check_command(string $command): void
{
    $commands = array_keys(COMMANDS_AND_ARGUMENTS);
    if(!in_array($command, $commands, true)) {
        $commandsStr = implode(', ', $commands);
        throw new \InvalidArgumentException("{$command} is invalid command! \nValid commands are:\t{$commandsStr}");
    }
}

function check_arguments_for(string $command): void
{
    global $argc, $argv;

    $commandsAndArguments = COMMANDS_AND_ARGUMENTS;
    $commandArguments = $commandsAndArguments[$command];

    $requiredArguments = array_map(fn($name) => "<{$name}>" ,array_keys(array_filter($commandArguments, fn($arg) => $arg === REQUIRED_STR)));
    $nullableArguments = array_map(fn($name) => "<?{$name}>" ,array_keys(array_filter($commandArguments, fn($arg) => $arg === NULLABLE_STR)));
    $requiredArgumentsStr = implode(' ', $requiredArguments);
    $nullableArgumentsStr = implode(' ', $nullableArguments);
    $commandUsageMessage = "Command usage for {$command} is:\nphp {$argv[0]} {$command} {$requiredArgumentsStr} {$nullableArgumentsStr}";
    if($argc - 2 < count($requiredArguments)) {
        throw new \InvalidArgumentException("Required parameters haven't implemented!\n{$commandUsageMessage}");
    }
    if($argc - 2 > count($commandArguments)) {
        throw new \InvalidArgumentException("Too much entry!\n{$commandUsageMessage}");
    }
}

function handle_command(string $command): void
{
    global $argv;
    $commandArguments = COMMANDS_AND_ARGUMENTS[$command];
    $userArguments = array_values(array_filter($argv, function ($arg_key) {
        return $arg_key > 1;
    }, ARRAY_FILTER_USE_KEY));

    if(count($userArguments) < count($commandArguments)) {
        $diff = count($commandArguments) - count($userArguments);
        for($i = 0; $i < $diff; $i++) {
            $userArguments[] = null;
        }
    }

    $commandStructure = array_combine(array_keys($commandArguments), $userArguments);
    switch ($command) {
        case "add": {
            handleAddCommand($commandStructure);
            break;
        }
        case "update": {
            handleUpdateCommand($commandStructure);
            break;
        }
        case "delete": {
            handleDeleteCommand($commandStructure);
            break;
        }
        case "mark-in-progress": {
            handleMarkInProgressCommand($commandStructure);
            break;
        }
        case "mark-done": {
            handleMarkDoneCommand($commandStructure);
            break;
        }
        case "list": {
            handleListCommand($commandStructure);
            break;
        }
    }

}

function handleAddCommand($commandStructure): void
{
    $description = $commandStructure['description'];

    $content = getFileContent();
    $contentArr = json_decode($content, true);
    $count = $contentArr['count'];

    $data = [
        'id' => $count + 1,
        'description' => $description,
        'status' => STATUS_TODO,
        'created_at' => time(),
        'updated_at' => null,
    ];

    $contentArr['tasks'][] = $data;
    $contentArr['count'] = $count + 1;
    $fpc = file_put_contents(FILENAME, json_encode($contentArr));
    if(empty($fpc)){
        throw new \RuntimeException("Somethings went wrong!");
    }
}

function handleListCommand($commandStructure): void
{
    $content = getFileContent();
    $content = json_decode($content, true);
    $tasks = $content['tasks'];
    $status = $commandStructure['status'];

    if($status !== null) {
        if(!in_array($status, STATUSES)) {
            $validStatusesStr = implode(',', STATUSES);
            throw new \InvalidArgumentException("Invalid status, valid statuses are:\n{$validStatusesStr}");
        }
        $tasks = array_filter($tasks, fn($task) => $task['status'] === $status);
    }

    if(empty($tasks)) {
        echo "Tasks are empty!\n";
        return;
    }

    foreach($tasks as $task)
    {
        $id = (int) $task['id'];
        $description = (string) $task['description'];
        $status = (string) $task['status'];
        $createdTimestamp = (int) $task['created_at'];
        $updatedTimestamp = (int) $task['updated_at'];
        $createdDate = (new DateTime())->setTimestamp($createdTimestamp)->format(DATE_ISO8601_EXPANDED);
        $updatedDate = $task['updated_at'] ? (new DateTime())->setTimestamp($updatedTimestamp)->format(DATE_ISO8601_EXPANDED) : null;

        echo "---------------\n";
        echo "TASK #{$id}\n";
        echo "Description: {$description}\n";
        echo "Status: {$status}\t";
        echo "Created: {$createdDate}\t";
        echo "Updated: {$updatedDate}\n";
    }
}

function handleMarkInProgressCommand($commandStructure): void
{
    $id = (int) $commandStructure['id'];
    $taskIndex = getTaskIndex($id);
    if($taskIndex === null) {
        throw new \Exception("Task not found");
    }

    $content = getFileContent();
    $contentArr = json_decode($content, true);
    $tasks = $contentArr['tasks'];

    $tasks[$taskIndex]['status'] = STATUS_IN_PROGRESS;
    $tasks[$taskIndex]['updated_at'] = time();

    $contentArr['tasks'] = $tasks;
    file_put_contents(FILENAME, json_encode($contentArr));
}

function handleMarkDoneCommand($commandStructure): void
{
    $id = (int) $commandStructure['id'];
    $taskIndex = getTaskIndex($id);
    if($taskIndex === null) {
        throw new \Exception("Task not found");
    }

    $content = getFileContent();
    $contentArr = json_decode($content, true);
    $tasks = $contentArr['tasks'];

    $tasks[$taskIndex]['status'] = STATUS_DONE;
    $tasks[$taskIndex]['updated_at'] = time();

    $contentArr['tasks'] = $tasks;
    file_put_contents(FILENAME, json_encode($contentArr));
}

function handleDeleteCommand($commandStructure): void
{
    $id = (int) $commandStructure['id'];
    $taskIndex = getTaskIndex($id);
    if($taskIndex === null) {
        throw new \Exception("Task not found");
    }

    $content = getFileContent();
    $contentArr = json_decode($content, true);
    $count = $contentArr['count'];

    unset($contentArr['tasks'][$taskIndex]);
    $contentArr['count'] = $count - 1;

    file_put_contents(FILENAME, json_encode($contentArr));
}

function handleUpdateCommand($args): void
{
    $id = (int) $args['id'];
    $description = (string) $args['description'];

    $taskIndex = getTaskIndex($id);
    if($taskIndex === null) {
        throw new \Exception("Task not found");
    }

    $content = getFileContent();
    $contentArr = json_decode($content, true);
    $tasks = $contentArr['tasks'];

    $tasks[$taskIndex]['description'] = $description;
    $tasks[$taskIndex]['updated_at'] = time();

    $contentArr['tasks'] = $tasks;
    file_put_contents(FILENAME, json_encode($contentArr));
}

function getTaskIndex(int $id): ?int
{
    $content = getFileContent();
    $contentArr = json_decode($content, true);

    $tasks = $contentArr['tasks'];
    foreach($tasks as $index => $task) {
        if($task['id'] === $id) {
            return $index;
        }
    }

    return null;
}

function getFileContent(): string
{
    $fileName = FILENAME;
    if(!file_exists($fileName))
    {
        $defaultData = ["tasks" => [], "count" => 0];
        $fpc = file_put_contents($fileName, json_encode($defaultData), FILE_APPEND);
        if($fpc === false) {
            throw new \RuntimeException("Somethings went wrong while file created.}");
        }
    }

    return file_get_contents($fileName);
}

