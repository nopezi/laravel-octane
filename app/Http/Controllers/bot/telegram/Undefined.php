<?php

namespace App\Http\Controllers\bot\telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramBotModel as TelegramModel;
use Illuminate\Http\Request;
use Longman\TelegramBot\Request as RequestTelegram;

class Undefined extends Controller
{

    
    public function __construct()
    {
        // parent::__construct();
        $this->telegram_model = new TelegramModel();
    }
    
    
    public function main($terima)
    {
        
        $this->telegram_model->setUserVar('FLAG', '', $terima['USER_ID']);
        $hasil_kirim = RequestTelegram::sendMessage([
            'chat_id' => $terima['USER_ID'],
            'text' => "Mohon maaf, kami belum mengerti apa yang anda maksud",
        ]);

        // log pesan terima dari user ke bot
        $this->telegram_model->botLogTerima($terima);
        // log pesan terkirim dari bot ke user
        $this->telegram_model->botLogKirim($hasil_kirim);
        
    }

}
