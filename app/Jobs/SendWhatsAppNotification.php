<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = env('WA_API_URL', 'https://api.fonnte.com/send');
        $token = env('WA_API_KEY', 'TOKEN_ANDA_DISINI');

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token,
            ])->post($url, [
                'target' => $this->phone,
                'message' => $this->message,
                'delay' => '2',
                'countryCode' => '62',
            ]);
            
            // Selalu log respon untuk debugging pengiriman
            \Illuminate\Support\Facades\Log::info("Fonnte API Response for {$this->phone}: " . $response->body());
            
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error("Failed to send WA reminder to {$this->phone}: " . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Exception when sending WA to {$this->phone}: " . $e->getMessage());
            throw $e; // Re-throw to allow queue retry if configured
        }
    }
}
