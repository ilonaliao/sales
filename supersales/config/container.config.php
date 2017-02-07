<?php
return [
    'settings' => [
        // show detail error info, online change to false!
        'displayErrorDetails' => true, 
        'viewTemplatesDirectory' => '../templates',
    ],
    'twig' => [
        'STATIC_VERSION' => STATIC_VERSION,
        'ROOT_PATH' => ROOT_PATH,
        'STATIC_WEB_PATH'=>STATIC_WEB_PATH,
        'AIDMA_WEB_PATH'=>AIDMA_WEB_PATH,
        'BUBBLE_STATIC_JS'=>BUBBLE_STATIC_JS,
        'WEB_PATH'=>WEB_PATH,
        'LOGIN_WEB_PATH'=>LOGIN_WEB_PATH
    ],
    'view' => function ($c) {
        $view = new \Slim\Views\Twig(
            $c['settings']['viewTemplatesDirectory'],
            [
                'cache' => false, // '../cache'
                'debug' => true,
            ]
        );

        // Instantiate and add Slim specific extension
        $extensionSetting = [
            new \Slim\Views\TwigExtension(
                $c['router'],
                $c['request']->getUri()
            ),
            new TwigArrayUnset(),
            new TwigCombineQueryarray()
        ];
        foreach ($extensionSetting as $key => $value) {
            $view->addExtension($value);
        }
        foreach ($c['twig'] as $name => $value) {
            $view->getEnvironment()->addGlobal($name, $value);
        }

        return $view;
    },
    'slack'=>function($c){
        $webhookUrl = 'https://hooks.slack.com/services/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';
        $channel = '#cteam-aws-alarm';
        $username = 'supersales';
        $slack = new Slack($webhookUrl,$channel,$username);
        return $slack;
    }
];