<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SendDiscordMessage
 *
 * @author Muhammad Wajahat
 *
 * @created_at Saturday, 4 January 2025
 *
 * @updated_at Saturday, 4 January 2025
 *
 * @description
 * This job handles sending messages to a specified Discord webhook. It is designed
 * to queue the requests for asynchronous execution, ensuring reliable and timely
 * delivery without blocking the main application flow.
 *
 * @purpose
 * - Automates the process of sending Discord notifications.
 * - Improves scalability by offloading message sending to a queue.
 * - Logs success and failure details for better traceability.
 */
class SendDiscordMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $discordLink;

    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $discordLink
     * @param string $message
     */
    public function __construct($discordLink, $message)
    {
        $this->discordLink = $discordLink;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send the Discord message
        $response = send_discord_msg($this->discordLink, $this->message);

        if ($response === false) {
            Log::error('Failed to send Discord message.', [
                'link' => $this->discordLink,
                'message' => $this->message,
            ]);
        } else {
            Log::info('Discord message sent successfully.', [
                'link' => $this->discordLink,
                'message' => $this->message,
                'response' => $response,
            ]);
        }
    }
}
