<?php

const REQUIRED_STR = 'required';
const NULLABLE_STR = 'nullable';
const STATUS_TODO = "todo";
const STATUS_IN_PROGRESS = "in-progress";
const STATUS_DONE = "done";
const FILENAME = "tasks.json";

const COMMANDS_AND_ARGUMENTS = [
    'add' => [
        'description' => REQUIRED_STR
    ],
    'update' => [
        'id' => REQUIRED_STR,
        'description' => REQUIRED_STR
    ],
    'delete' => [
        'id' => REQUIRED_STR
    ],
    'mark-in-progress' => [
        'id' => REQUIRED_STR
    ],
    'mark-done' => [
        'id' => REQUIRED_STR
    ],
    'list' => [
        'status' => NULLABLE_STR
    ]
];

const STATUSES = [
    STATUS_TODO,
    STATUS_IN_PROGRESS,
    STATUS_DONE
];

