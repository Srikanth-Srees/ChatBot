<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Meeting;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;


class SendWhatsAppReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-whats-app-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    


public function handle()
{
    // Get the current datetime in Asia/Kolkata timezone
    $currentDateTime = now()->addHour()->setTimezone('Asia/Kolkata');

    // Get the current date
    $currentDate = $currentDateTime->toDateString();

    // Get the current hour in 24-hour format (e.g., 13:00, 14:00)
    $currentHour = $currentDateTime->format('H');
    
    // Calculate the next hour in 24-hour format
    $nextHour = $currentDateTime->copy()->addHour()->format('H');

    // Fetch meetings scheduled for the current date and within the current hour
    $meetings = Meeting::whereDate('date', $currentDate)
                        ->whereRaw("SUBSTRING_INDEX(time, ' - ', 1) BETWEEN ? AND ?", [$currentHour, $nextHour])
                        ->get();
      
    // Iterate through the fetched meetings and send reminders
    foreach ($meetings as $meeting) {
                        $phone = $meeting->phone;
                        $message = "⏰ **Reminder:**\n\nDon't forget, you have an important meeting coming up soon at *{$meeting->time}*.\n\nThis meeting is for *{$meeting->service}*.\n\n Your participation is valuable!\n\nSee you there!";
        
                    
                        // Send the message using WhatsAppCloudApi
                        $whatsapp_cloud_api = new WhatsAppCloudApi([
                            'from_phone_number_id' =>$_ENV['FROM_PHONE_NUMBER_ID'],
                            'access_token' => $_ENV['ACCESS_TOKEN'],
                        ]);
                        $whatsapp_cloud_api->sendTextMessage($phone, $message);
                            
    }
}

}
