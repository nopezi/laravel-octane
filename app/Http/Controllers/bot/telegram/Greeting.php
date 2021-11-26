<?php 

namespace App\Http\Controllers\bot\telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramBotModel as TelegramModel;
use Illuminate\Http\Request;
use Longman\TelegramBot\Request as RequestTelegram;

class Greeting extends Controller
{

    public function __construct()
    {
        // parent::__construct();
        $this->telegram_model = new TelegramModel();
    }

    public function main($terima)
    {

        $intent[] = "hai";
        $intent[] = "assalamualaikum";
        $intent[] = "selamat malam";
        $intent[] = "selamat pagi";
        $intent[] = "selamat siang";
        $intent[] = "selamat sore";
        $intent[] = "salam";
        
		if (in_array($terima['PESAN'], $intent)) {
            
            $hour = date("G", time());
            if ($hour >= '00' && $hour <= '11') {
                $waktu = 'Pagi';
            } else if($hour >= '12' && $hour <= '14') {
                $waktu = 'Siang';
            } else if($hour >= '15' && $hour <= '17') {
                $waktu = 'Sore';
            } else if($hour >= '18' && $hour <= '23') {
                $waktu = 'Malam';
            }

            $pesan_random[] = "hai selamat datang";
            $pesan_random[] ="Selamat {$waktu} ada yang bisa kami bantu";
            $pr = array_rand($pesan_random);
            $pesan = $pesan_random[$pr];

            $hasil_kirim = RequestTelegram::sendMessage([
                'chat_id' => $terima['USER_ID'],
                'text' => $pesan,
            ]);

            // log pesan terima dari user ke bot
            $this->telegram_model->botLogTerima($terima);
            // log pesan terkirim dari bot ke user
            $this->telegram_model->botLogKirim($hasil_kirim);

            return true;
            
        }
        
    }
    
}
