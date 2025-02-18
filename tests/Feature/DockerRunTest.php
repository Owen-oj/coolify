<?php

test('ConvertCapAdd', function () {
    $input = '--cap-add=NET_ADMIN --cap-add=NET_RAW --cap-add SYS_ADMIN';
    $output = convert_docker_run_to_compose($input);
    expect($output)->toBe([
        'cap_add' => ['NET_ADMIN', 'NET_RAW', 'SYS_ADMIN'],
    ])->ray();
});

test('ConvertPrivilegedAndInit', function () {
    $input = '---privileged --init';
    $output = convert_docker_run_to_compose($input);
    expect($output)->toBe([
        'privileged' => true,
        'init' => true,
    ])->ray();
});

test('ConvertUlimit', function () {
    $input = '--ulimit nofile=262144:262144';
    $output = convert_docker_run_to_compose($input);
    expect($output)->toBe([
        'ulimits' => [
            'nofile' => [
                'soft' => '262144',
                'hard' => '262144',
            ],
        ],
    ])->ray();
});
