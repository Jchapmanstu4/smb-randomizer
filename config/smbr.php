<?php

/*
 * Config for the randomizer
 */

return [
    'randomizer-default' => [
        'colorscheme.mario' => 'random',
        'colorscheme.luigi' => 'random',
        'colorscheme.fire' => 'random',
        'pipe-transitions' => 'remove',
        'shuffle-levels' => 'all',
        'enemies' => 'randomize.pools',
        'blocks' => 'randomize.all',
        'bowser-hitpoints' => 'true',
        'bowser-abilities' => 'normal',
    ],

    'randomizer' => [
        'colorscheme' => [
            'mario' => [
                'vanilla' => 'Mario vanilla color scheme',
                'random' => 'Totally random color scheme',
            ],
            'luigi' => [
                'vanilla' => 'Luigi vanilla color scheme',
                'random' => 'Totally random color scheme',
            ],
            'fire' => [
                'vanilla' => 'Fire Mario/Luigi vanilla color scheme',
                'random' => 'Totally random color scheme',
            ],
        ],
        'pipe-transitions' => [
            'remove' => 'Remove pipe transitions',
            'keep' => 'Keep pipe transitions',
        ],
        'shuffle-levels' => [
            'all' => 'Shuffle all levels',
            'worlds' => 'Shuffle world order only',
            'none' => 'Do not shuffle levels',
        ],
        'normal-world-length' => [
            'true' => 'Each world has 4 levels',
            'false' => 'Worlds can have varying lengths',
        ],
        'enemies' => [
            'randomize-full' => 'Randomize all enemies (within reason)',
            'randomize-pools' => 'Randomize enemies within smaller pools of related or similar enemies',
            'randomize-none' => 'Do not randomize enemies',
            'shuffle' => 'not implemented',
        ],
        'blocks' => [
            'randomize-all' => 'Randomize all blocks',
            'randomize-powerups' => 'Randomize blocks normally containing a power up',
            'randomize-grouped' => 'Randomze blocks in groups',
            'randomize-coins' => 'Replace all power ups with coins',
            'randomize-none' => 'Do not randomize blocks',
            'shuffle' => 'not implemented',
        ],
        'bowser-abilities' => [
            'true' => 'Randomize which world Bowser starts throwing hammers and breathing fire',
            'false' => "Do not randomize Bowser's abilities",
        ],
        'bowser-hitpoints' => [
            'normal' => 'Bowser has 5 hitpoints',
            'easy' => 'Bowser has 1-5 hitpoints',
            'medium' => 'Bowser has 5-10 hitpoints',
            'hard' => 'Bowser has 10-20 hitpoints',
        ],
    ],
];