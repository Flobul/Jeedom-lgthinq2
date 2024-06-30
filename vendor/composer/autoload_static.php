<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitLGThinQ2
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'PhpMqtt\\Client\\' => 15,
        ),
        'M' => 
        array (
            'MyCLabs\\Enum\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'PhpMqtt\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-mqtt/client/src',
        ),
        'MyCLabs\\Enum\\' => 
        array (
            0 => __DIR__ . '/..' . '/myclabs/php-enum/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'MyCLabs\\Enum\\Enum' => __DIR__ . '/..' . '/myclabs/php-enum/src/Enum.php',
        'MyCLabs\\Enum\\PHPUnit\\Comparator' => __DIR__ . '/..' . '/myclabs/php-enum/src/PHPUnit/Comparator.php',
        'PhpMqtt\\Client\\Concerns\\GeneratesRandomClientIds' => __DIR__ . '/..' . '/php-mqtt/client/src/Concerns/GeneratesRandomClientIds.php',
        'PhpMqtt\\Client\\Concerns\\OffersHooks' => __DIR__ . '/..' . '/php-mqtt/client/src/Concerns/OffersHooks.php',
        'PhpMqtt\\Client\\Concerns\\TranscodesData' => __DIR__ . '/..' . '/php-mqtt/client/src/Concerns/TranscodesData.php',
        'PhpMqtt\\Client\\Concerns\\ValidatesConfiguration' => __DIR__ . '/..' . '/php-mqtt/client/src/Concerns/ValidatesConfiguration.php',
        'PhpMqtt\\Client\\Concerns\\WorksWithBuffers' => __DIR__ . '/..' . '/php-mqtt/client/src/Concerns/WorksWithBuffers.php',
        'PhpMqtt\\Client\\ConnectionSettings' => __DIR__ . '/..' . '/php-mqtt/client/src/ConnectionSettings.php',
        'PhpMqtt\\Client\\Contracts\\MessageProcessor' => __DIR__ . '/..' . '/php-mqtt/client/src/Contracts/MessageProcessor.php',
        'PhpMqtt\\Client\\Contracts\\MqttClient' => __DIR__ . '/..' . '/php-mqtt/client/src/Contracts/MqttClient.php',
        'PhpMqtt\\Client\\Contracts\\Repository' => __DIR__ . '/..' . '/php-mqtt/client/src/Contracts/Repository.php',
        'PhpMqtt\\Client\\Exceptions\\ClientNotConnectedToBrokerException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/ClientNotConnectedToBrokerException.php',
        'PhpMqtt\\Client\\Exceptions\\ConfigurationInvalidException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/ConfigurationInvalidException.php',
        'PhpMqtt\\Client\\Exceptions\\ConnectingToBrokerFailedException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/ConnectingToBrokerFailedException.php',
        'PhpMqtt\\Client\\Exceptions\\DataTransferException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/DataTransferException.php',
        'PhpMqtt\\Client\\Exceptions\\InvalidMessageException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/InvalidMessageException.php',
        'PhpMqtt\\Client\\Exceptions\\MqttClientException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/MqttClientException.php',
        'PhpMqtt\\Client\\Exceptions\\PendingMessageAlreadyExistsException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/PendingMessageAlreadyExistsException.php',
        'PhpMqtt\\Client\\Exceptions\\PendingMessageNotFoundException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/PendingMessageNotFoundException.php',
        'PhpMqtt\\Client\\Exceptions\\ProtocolNotSupportedException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/ProtocolNotSupportedException.php',
        'PhpMqtt\\Client\\Exceptions\\ProtocolViolationException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/ProtocolViolationException.php',
        'PhpMqtt\\Client\\Exceptions\\RepositoryException' => __DIR__ . '/..' . '/php-mqtt/client/src/Exceptions/RepositoryException.php',
        'PhpMqtt\\Client\\Logger' => __DIR__ . '/..' . '/php-mqtt/client/src/Logger.php',
        'PhpMqtt\\Client\\Message' => __DIR__ . '/..' . '/php-mqtt/client/src/Message.php',
        'PhpMqtt\\Client\\MessageProcessors\\BaseMessageProcessor' => __DIR__ . '/..' . '/php-mqtt/client/src/MessageProcessors/BaseMessageProcessor.php',
        'PhpMqtt\\Client\\MessageProcessors\\Mqtt311MessageProcessor' => __DIR__ . '/..' . '/php-mqtt/client/src/MessageProcessors/Mqtt311MessageProcessor.php',
        'PhpMqtt\\Client\\MessageProcessors\\Mqtt31MessageProcessor' => __DIR__ . '/..' . '/php-mqtt/client/src/MessageProcessors/Mqtt31MessageProcessor.php',
        'PhpMqtt\\Client\\MessageType' => __DIR__ . '/..' . '/php-mqtt/client/src/MessageType.php',
        'PhpMqtt\\Client\\MqttClient' => __DIR__ . '/..' . '/php-mqtt/client/src/MqttClient.php',
        'PhpMqtt\\Client\\PendingMessage' => __DIR__ . '/..' . '/php-mqtt/client/src/PendingMessage.php',
        'PhpMqtt\\Client\\PublishedMessage' => __DIR__ . '/..' . '/php-mqtt/client/src/PublishedMessage.php',
        'PhpMqtt\\Client\\Repositories\\MemoryRepository' => __DIR__ . '/..' . '/php-mqtt/client/src/Repositories/MemoryRepository.php',
        'PhpMqtt\\Client\\SubscribeRequest' => __DIR__ . '/..' . '/php-mqtt/client/src/SubscribeRequest.php',
        'PhpMqtt\\Client\\Subscription' => __DIR__ . '/..' . '/php-mqtt/client/src/Subscription.php',
        'PhpMqtt\\Client\\UnsubscribeRequest' => __DIR__ . '/..' . '/php-mqtt/client/src/UnsubscribeRequest.php',
        'Psr\\Log\\AbstractLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/AbstractLogger.php',
        'Psr\\Log\\InvalidArgumentException' => __DIR__ . '/..' . '/psr/log/Psr/Log/InvalidArgumentException.php',
        'Psr\\Log\\LogLevel' => __DIR__ . '/..' . '/psr/log/Psr/Log/LogLevel.php',
        'Psr\\Log\\LoggerAwareInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareInterface.php',
        'Psr\\Log\\LoggerAwareTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareTrait.php',
        'Psr\\Log\\LoggerInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerInterface.php',
        'Psr\\Log\\LoggerTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerTrait.php',
        'Psr\\Log\\NullLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/NullLogger.php',
        'Psr\\Log\\Test\\DummyTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/DummyTest.php',
        'Psr\\Log\\Test\\LoggerInterfaceTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
        'Psr\\Log\\Test\\TestLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/TestLogger.php',
        'Stringable' => __DIR__ . '/..' . '/myclabs/php-enum/stubs/Stringable.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitLGThinQ2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitLGThinQ2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitLGThinQ2::$classMap;

        }, null, ClassLoader::class);
    }
}
