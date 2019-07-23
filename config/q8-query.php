<?php

return [

    /**
     * this array contain the namespaces that holds the models
     * the lookup is prioritized by ordering where the first namespace is highest priority
     */
    "namespaces" =>
        [
            "App\Models"
        ],

    /**
     * url grouped by prefix ex: www.example.com/{url_prefix}/User
     */
    "url_prefix" => 'Q8Query',

    /**
     * param key used for filtering ex:  www.example.com/User?{filterer}=name eq "sss"
     */
    "filterer" => 'filter',

    /**
     * tokens key words and are replaceable to suit any project
     * ex: `www.example.com/User?filter=name {eq} "ssss"`
     * the following array allows to replace the `eq` in the past example with any keyword that suits the project
     *
     * Cautions: dont replace it with an invalid url characters like {=, ?, &) as it will cause the url to crash
     */
    "tokens" => [
        'or' => 'or',
        'and' => 'and',
        '='   => 'eq',
        '!='  => 'ne',
        '>'   => 'gt',
        '>='   => 'ge',
        '<'   => 'lt',
        '<='   => 'le',
        'has' => 'has',
    ],
];