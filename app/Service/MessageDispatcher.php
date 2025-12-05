<?php

namespace App\Service;

use App\Jobs\QueueMessageJob;
use Illuminate\Support\Facades\Log;

class MessageDispatcher
{
    /**
     * Dispatch a message to RabbitMQ
     *
     * @param array $message The message data
     * @param string|null $routingKey Routing key for message routing
     * @param string|null $exchange Exchange name (defaults to config)
     * @param array $options Additional options
     * @return void
     */
    public static function toRabbitMQ(
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ): void {
        self::dispatch('rabbitmq', $message, $routingKey, $exchange, $options);
    }

    /**
     * Dispatch a message to Redis
     *
     * @param array $message The message data
     * @param string|null $routingKey Not used for Redis but kept for consistency
     * @param string|null $exchange Not used for Redis but kept for consistency
     * @param array $options Additional options (redis_key, use_streams, etc.)
     * @return void
     */
    public static function toRedis(
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ): void {
        self::dispatch('redis', $message, $routingKey, $exchange, $options);
    }

    /**
     * Dispatch a message to database queue (for testing/fallback)
     *
     * @param array $message The message data
     * @param string|null $routingKey Not used for database but kept for consistency
     * @param string|null $exchange Not used for database but kept for consistency
     * @param array $options Additional options
     * @return void
     */
    public static function toDatabase(
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ): void {
        self::dispatch('database', $message, $routingKey, $exchange, $options);
    }

    /**
     * Dispatch authentication success event
     *
     * @param array $authData Authentication data (email, ip, user_agent, etc.)
     * @param string $queueType Queue type (rabbitmq, redis, database)
     * @return void
     */
    public static function authSuccess(array $authData, string $queueType = 'rabbitmq'): void
    {
        $message = [
            'event_type' => 'user_authentication',
            'timestamp' => $authData['timestamp'] ?? now()->toIso8601String(),
            'user_email' => $authData['email'],
            'status' => 'success',
            'ip_address' => $authData['ip_address'] ?? 'unknown',
            'user_agent' => $authData['user_agent'] ?? 'unknown',
            'metadata' => [
                'source' => 'laravel_app',
                'version' => config('app.version', '1.0.0'),
            ]
        ];

        $routingKey = 'auth.login.success';

        self::dispatch($queueType, $message, $routingKey);
    }

    /**
     * Dispatch authentication failure event
     *
     * @param array $authData Authentication data (email, ip, user_agent, failure_reason, etc.)
     * @param string $queueType Queue type (rabbitmq, redis, database)
     * @return void
     */
    public static function authFailure(array $authData, string $queueType = 'rabbitmq'): void
    {
        $message = [
            'event_type' => 'user_authentication',
            'timestamp' => $authData['timestamp'] ?? now()->toIso8601String(),
            'user_email' => $authData['email'],
            'status' => 'failed',
            'ip_address' => $authData['ip_address'] ?? 'unknown',
            'user_agent' => $authData['user_agent'] ?? 'unknown',
            'failure_reason' => $authData['failure_reason'] ?? 'unknown',
            'metadata' => [
                'source' => 'laravel_app',
                'version' => config('app.version', '1.0.0'),
            ]
        ];

        $routingKey = 'auth.login.failed';

        self::dispatch($queueType, $message, $routingKey);
    }

    /**
     * Dispatch user update event
     *
     * @param array $userData User data that was updated
     * @param string $queueType Queue type (rabbitmq, redis, database)
     * @return void
     */
    public static function userUpdated(array $userData, string $queueType = 'rabbitmq'): void
    {
        $message = [
            'event_type' => 'user_updated',
            'timestamp' => now()->toIso8601String(),
            'user_id' => $userData['id'],
            'user_email' => $userData['email'] ?? null,
            'changes' => $userData['changes'] ?? [],
            'metadata' => [
                'source' => 'laravel_app',
                'version' => config('app.version', '1.0.0'),
            ]
        ];

        $routingKey = 'user.updated';

        self::dispatch($queueType, $message, $routingKey);
    }

    /**
     * Dispatch generic event
     *
     * @param string $eventType Type of event
     * @param array $data Event data
     * @param string $queueType Queue type (rabbitmq, redis, database)
     * @param string|null $routingKey Custom routing key
     * @return void
     */
    public static function event(
        string $eventType,
        array $data,
        string $queueType = 'rabbitmq',
        ?string $routingKey = null
    ): void {
        $message = array_merge($data, [
            'event_type' => $eventType,
            'timestamp' => $data['timestamp'] ?? now()->toIso8601String(),
            'metadata' => array_merge($data['metadata'] ?? [], [
                'source' => 'laravel_app',
                'version' => config('app.version', '1.0.0'),
            ])
        ]);

        $routingKey = $routingKey ?? $eventType;

        self::dispatch($queueType, $message, $routingKey);
    }

    /**
     * Core dispatch method
     *
     * @param string $queueType Queue type
     * @param array $message Message data
     * @param string|null $routingKey Routing key
     * @param string|null $exchange Exchange name
     * @param array $options Additional options
     * @return void
     */
    private static function dispatch(
        string $queueType,
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ): void {
        try {
            QueueMessageJob::dispatch($queueType, $message, $routingKey, $exchange, $options);

            Log::debug('Dispatched message to queue', [
                'queue_type' => $queueType,
                'routing_key' => $routingKey,
                'exchange' => $exchange,
                'message_keys' => array_keys($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to dispatch message to queue', [
                'queue_type' => $queueType,
                'routing_key' => $routingKey,
                'exchange' => $exchange,
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            // Don't throw - we want the application to continue working
            // even if queue dispatching fails
        }
    }

    /**
     * Dispatch message synchronously (for testing)
     *
     * @param string $queueType Queue type
     * @param array $message Message data
     * @param string|null $routingKey Routing key
     * @param string|null $exchange Exchange name
     * @param array $options Additional options
     * @return void
     */
    public static function dispatchSync(
        string $queueType,
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ): void {
        try {
            $job = new QueueMessageJob($queueType, $message, $routingKey, $exchange, $options);
            $job->handle();

        } catch (\Exception $e) {
            Log::error('Failed to dispatch message synchronously', [
                'queue_type' => $queueType,
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            throw $e;
        }
    }
}
