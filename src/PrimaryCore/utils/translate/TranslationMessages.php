<?php

namespace PrimaryCore\utils\translate;

class TranslationMessages {

    /** @var array */
    public const DEFAULT_TRANSLATIONS = [
        'errorMessage' => 'There has been {var1} error and {var2}.'
        // Add more default translations here
    ];

    /** @var array */
    public const SERVER_TRANSLATIONS = [
        'onJoin' => '{BOLD}{GREEN}{USERNAME}',
        'banMessage' => 'You have been banned from the server. Reason: {REASON}',
        'banBroadcast' => 'Player {PLAYER} has been banned by {SENDER} for reason: {REASON}'
        // Add more server-related translations here
    ];

    /** @var array */
    public const ADMIN_TRANSLATIONS = [
        // Add administrator-related translations here
    ];

    /** @var array */
    public const STAFF_TRANSLATIONS = [
        // Add staff-related translations here
    ];

    /** @var array */
    public const UTIL_TRANSLATIONS = [
        // Add utility-related translations here
    ];

    /** @var array */
    public const GAMEMODE_TRANSLATIONS = [
        // Add gamemode-related translations here
    ];
}
