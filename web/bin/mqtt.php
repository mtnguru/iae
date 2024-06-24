#!/usr/bin/env php

<?php
require('../autoload.php');
use \PhpMqtt\Client\MqttClient;

$mqtt = [
  "url" => "labtime.org",
  "clientId" => "mqtt_" . rand(1, 10000000),
  "port"     => 1883,
  "username" => "data",
  "password" => "datawp",
  "clean_session" => false,
];

$connectionSettings = (new ConnectionSettings)
  ->setUsername($mqtt->username)
  ->setPassword($mqtt->password)
  ->setKeepAliveInterval(60)
  ->setLastWillTopic('emqx/test/last-will')
  ->setLastWillMessage('client disconnect')
  ->setLastWillQualityOfService(1);


$mqtt_version = MqttClient::MQTT_3_1_1;

print ("url " . $mqtt["url"] . "\n");
print ("clientId " . $mqtt["clientId"] . "\n");
print ("mqtt_version " . $mqtt_version . "\n\n");
