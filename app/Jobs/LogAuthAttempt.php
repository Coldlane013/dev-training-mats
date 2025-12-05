<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogAuthAttempt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $status; // 'success' or 'failed'
    protected $ipAddress;
    protected $userAgent;
    protected $timestamp;
    protected $failureReason; // null for success, 'invalid_credentials' or 'user_not_found' for failure

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $status ('success' or 'failed')
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @param string|null $failureReason
     */
    public function __construct(
        string $email,
        string $status,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $failureReason = null
    ) {
        $this->email = $email;
        $this->status = $status;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->failureReason = $failureReason;
        $this->timestamp = Carbon::now();
        $this->queue = 'auth-logs';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logData = [
            'timestamp' => $this->timestamp->toIso8601String(),
            'email' => $this->email,
            'status' => $this->status,
            'ip_address' => $this->ipAddress ?? 'unknown',
            'user_agent' => $this->userAgent ?? 'unknown',
        ];

        if ($this->status === 'failed') {
            $logData['failure_reason'] = $this->failureReason ?? 'unknown';
        }

        // Log to dedicated auth channel
        Log::channel('auth')->info("Authentication attempt - {$this->status}", $logData);

        // Dispatch to message broker / event system
        $this->dispatchToMessageBroker($logData);

        // Also log to default channel with summary
        $summary = $this->status === 'success'
            ? "User {$this->email} logged in successfully"
            : "Failed login attempt for {$this->email} ({$this->failureReason})";

        Log::info($summary, $logData);
    }

    /**
     * Dispatch event to message broker (e.g., Redis, RabbitMQ, Kafka).
     * This is a placeholder for your message broker integration.
     *
     * @param array $logData
     */
    private function dispatchToMessageBroker(array $logData): void
    {
        // Example: Dispatch to Redis queue for event streaming
        // You can replace this with your preferred message broker

        try {
            // Option 1: Redis Streams (if configured)
            // redis()->xadd('auth_events', '*', $logData);

            // Option 2: Event Broadcasting (for real-time notifications)
            // broadcast(new AuthAttemptLogged($logData));

            // Option 3: Direct HTTP webhook (if external service)
            // Http::post('https://your-broker-url/api/events', $logData);

            // For now, we log that it would be sent
            Log::channel('auth')->debug('Message broker event would be dispatched', [
                'event_type' => 'auth_attempt',
                'data' => $logData,
            ]);
        } catch (\Exception $e) {
            Log::channel('auth')->error('Failed to dispatch to message broker', [
                'error' => $e->getMessage(),
                'data' => $logData,
            ]);
        }
    }
}
