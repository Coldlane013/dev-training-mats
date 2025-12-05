<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable as BusQueueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Exception;

class QueueMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, BusQueueable, SerializesModels;

    protected $queueType;
    protected $message;
    protected $routingKey;
    protected $exchange;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @param string $queueType The type of queue (rabbitmq, redis, etc.)
     * @param array $message The message data to send
     * @param string|null $routingKey Routing key for message routing
     * @param string|null $exchange Exchange name (for RabbitMQ)
     * @param array $options Additional options for the queue system
     */
    public function __construct(
        string $queueType,
        array $message,
        ?string $routingKey = null,
        ?string $exchange = null,
        array $options = []
    ) {
        $this->queueType = $queueType;
        $this->message = $message;
        $this->routingKey = $routingKey;
        $this->exchange = $exchange;
        $this->options = $options;

        // Set the queue based on type
        $this->onQueue($this->getQueueName());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            switch ($this->queueType) {
                case 'rabbitmq':
                    $this->sendToRabbitMQ();
                    break;
                case 'redis':
                    $this->sendToRedis();
                    break;
                case 'database':
                    $this->sendToDatabase();
                    break;
                default:
                    Log::warning('Unsupported queue type: ' . $this->queueType, [
                        'message' => $this->message
                    ]);
                    throw new Exception('Unsupported queue type: ' . $this->queueType);
            }

            Log::info('Successfully sent message to queue', [
                'queue_type' => $this->queueType,
                'routing_key' => $this->routingKey,
                'exchange' => $this->exchange,
                'message_keys' => array_keys($this->message)
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send message to queue', [
                'queue_type' => $this->queueType,
                'error' => $e->getMessage(),
                'message' => $this->message,
                'routing_key' => $this->routingKey,
                'exchange' => $this->exchange
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Send message to RabbitMQ
     */
    private function sendToRabbitMQ(): void
    {
        // Check if php-amqplib is available
        if (!class_exists('\PhpAmqpLib\Connection\AMQPStreamConnection')) {
            throw new Exception('php-amqplib package is not installed');
        }

        $config = config('queue.connections.rabbitmq', []);

        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 5672,
            $config['user'] ?? 'guest',
            $config['password'] ?? 'guest',
            $config['vhost'] ?? '/'
        );

        $channel = $connection->channel();

        // Declare exchange if specified
        $exchange = $this->exchange ?? $config['exchange'] ?? 'default_exchange';
        $exchangeType = $config['exchange_type'] ?? 'topic';

        $channel->exchange_declare($exchange, $exchangeType, false, true, false);

        // Create and publish message
        $messageBody = json_encode($this->message);
        $routingKey = $this->routingKey ?? '';

        $message = new \PhpAmqpLib\Message\AMQPMessage($messageBody, [
            'content_type' => 'application/json',
            'delivery_mode' => \PhpAmqpLib\Message\AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'timestamp' => time(),
        ]);

        $channel->basic_publish($message, $exchange, $routingKey);

        $channel->close();
        $connection->close();
    }

    /**
     * Send message to Redis
     */
    private function sendToRedis(): void
    {
        $redisKey = $this->options['redis_key'] ?? 'queue:messages';

        if (isset($this->options['use_streams']) && $this->options['use_streams']) {
            // Use Redis Streams
            Redis::xadd($redisKey, '*', $this->message);
        } else {
            // Use regular Redis list
            Redis::rpush($redisKey, json_encode($this->message));
        }
    }

    /**
     * Send message to database (for testing or fallback)
     */
    private function sendToDatabase(): void
    {
        // Store in database for testing purposes
        \DB::table('queue_messages')->insert([
            'queue_type' => $this->queueType,
            'message' => json_encode($this->message),
            'routing_key' => $this->routingKey,
            'exchange' => $this->exchange,
            'options' => json_encode($this->options),
            'created_at' => now(),
            'processed_at' => now(),
        ]);
    }

    /**
     * Get queue name based on type
     */
    private function getQueueName(): string
    {
        return match ($this->queueType) {
            'rabbitmq' => 'rabbitmq',
            'redis' => 'redis',
            'database' => 'database',
            default => 'default',
        };
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        Log::error('QueueMessageJob failed permanently', [
            'queue_type' => $this->queueType,
            'message' => $this->message,
            'error' => $exception->getMessage(),
        ]);
    }
}
