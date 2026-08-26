<?php

const REQUIRED_STR = 'required';
const NULLABLE_STR = 'nullable';
const STATUS_TODO = "todo";
const STATUS_IN_PROGRESS = "in-progress";
const STATUS_DONE = "done";
const COMMAND_ADD = "add";
const COMMAND_UPDATE = "update";
const COMMAND_DELETE = "delete";
const COMMAND_MARK_IN_PROGRESS = "mark-in-progress";
const COMMAND_MARK_DONE = "mark-done";
const COMMAND_LIST = "list";

const ARG_ID = "id";
const ARG_DESCRIPTION = "description";
const ARG_STATUS = "status";
const ARG_CREATED_AT = "created_at";
const ARG_UPDATED_AT = "updated_at";

const FILENAME = "tasks.json";

const COMMANDS_AND_ARGUMENTS = [
    COMMAND_ADD => [
        ARG_DESCRIPTION => REQUIRED_STR
    ],
    COMMAND_UPDATE => [
        ARG_ID => REQUIRED_STR,
        ARG_DESCRIPTION => REQUIRED_STR
    ],
    COMMAND_DELETE => [
        ARG_ID => REQUIRED_STR
    ],
    COMMAND_MARK_IN_PROGRESS => [
        ARG_ID => REQUIRED_STR
    ],
    COMMAND_MARK_DONE => [
        ARG_ID => REQUIRED_STR
    ],
    COMMAND_LIST => [
        ARG_STATUS => NULLABLE_STR
    ]
];

const STATUSES = [
    STATUS_TODO,
    STATUS_IN_PROGRESS,
    STATUS_DONE
];

