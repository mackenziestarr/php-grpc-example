<?php
/*
 *
 * Copyright 2015 gRPC authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

// php:generate protoc --proto_path=./../protos   --php_out=./   --grpc_out=./ --plugin=protoc-gen-grpc=./../../bins/opt/grpc_php_plugin ./../protos/helloworld.proto

require dirname(__FILE__).'/../vendor/autoload.php';

@include_once dirname(__FILE__).'/../Helloworld/GreeterClient.php';
@include_once dirname(__FILE__).'/../Helloworld/HelloReply.php';
@include_once dirname(__FILE__).'/../Helloworld/HelloRequest.php';
@include_once dirname(__FILE__).'/../GPBMetadata/Helloworld.php';

$name = !empty($argv[1]) ? $argv[1] : 'world';

$client = new Helloworld\GreeterClient('java-grpc-server:50051', [
    'credentials' => Grpc\ChannelCredentials::createInsecure(),
]);

$request = new Helloworld\HelloRequest();
$request->setName($name);
$start_time = microtime(true);
list($reply, $status) = $client->SayHello($request)->wait();

printf("pid %d\tmessage %s\ttime %.2f ms\n",
    getmypid(), 
    $reply->getMessage(),
    (microtime(true) - $start_time) * 1000);
