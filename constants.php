<?php

const REQUIRED_STR = 'required';
const NULLABLE_STR = 'nullable';

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
    ],
    'my_command' => [
        'id' => REQUIRED_STR,
        'description' => REQUIRED_STR,
        'status' => NULLABLE_STR,
    ],
];

const STATUSES = [
    'todo',
    'in_progress',
    'done'
];

