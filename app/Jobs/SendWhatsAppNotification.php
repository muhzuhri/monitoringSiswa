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
        $url = config('services.wa.url');
        $token = config('services.wa.key');

        // Format nomor HP ke format internasional (62...)
        $phone = preg_replace('/\D/', '', $this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token,
            ])->post($url, [
                'target' => $phone,
                'message' => $this->message,
                'delay' => '2',
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
