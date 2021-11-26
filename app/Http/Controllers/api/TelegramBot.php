<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\bot\telegram\Greeting;
use App\Http\Controllers\bot\telegram\Selesai;
use App\Http\Controllers\bot\telegram\Sholat;
use App\Http\Controllers\bot\telegram\Undefined;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request as RequestTelegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\TelegramLog;

class TelegramBot extends Controller
{

    
    public function __construct()
    {
        // parent::__construct();
        if (!defined('API_KEY')) define('API_KEY', '721340366:AAE3bjqCcmbG4zGDwHZtm6Ld9xq9VN1-kdc');
        if (!defined('BOT_NAME')) define("BOT_NAME", "nopezi1bot");
        $this->telegram = new Telegram(API_KEY, BOT_NAME);
    }

    public function coba(Request $request)
    {
        
        $pesan = true;
        $pesan .= false;
        $pesan .= true;
        $PESAN = 'MASOK';
        echo $pesan . ' ' . $PESAN;
        
    }
    
    public function hook(Request $request)
    {

        $input = json_encode($request->all());
        $update = new Update($request->all());
        $sholat = new Sholat();
        $selesai = new Selesai();
        $undefined = new Undefined();
        $greeting = new Greeting();

        if (!defined('PESAN')) define("PESAN", $update->getMessage()->getText());
        if (!defined('USER_ID')) define("USER_ID", $update->getMessage()->getFrom()->getId());
        if (!defined('FIRST_NAME')) define("FIRST_NAME", $update->getMessage()->getFrom()->getFirstName());
        if (!defined('LAST_NAME')) define("LAST_NAME", $update->getMessage()->getFrom()->getLastName());
        if (!defined('IS_BOT')) define("IS_BOT", $update->getMessage()->getFrom()->getIsBot());
        if (!defined('MESSAGE_ID')) define("MESSAGE_ID", $update->getMessage()->getMessageId());

        $terima = [
            'PESAN' => $update->getMessage()->getText(),
            'USER_ID' => $update->getMessage()->getFrom()->getId(),
            'FIRST_NAME' => $update->getMessage()->getFrom()->getFirstName(),
            'LAST_NAME' => $update->getMessage()->getFrom()->getLastName(),
            'MESSAGE_ID' => $update->getMessage()->getMessageId(),
        ];
        $is_bot = $update->getMessage()->getFrom()->getIsBot();

        $hasil = false;
        if ($is_bot == false) {
            $hasil .= $greeting->main($terima);
            $hasil .= $sholat->main($terima);
            $hasil .= $selesai->main($terima);
        }

        if (empty($hasil)) {
            $undefined->main($terima);
        }

        $this->telegram->handle($input);

    }

    public function setWebhook(Request $request)
    {
        $url = $request->get('url');

        try {
            // Create Telegram API object
            $allowed_updates = [
                Update::TYPE_MESSAGE,
                Update::TYPE_CHANNEL_POST,
                // etc.
            ];
            $hasil = $this->telegram->setWebhook($url, ['allowed_updates' => $allowed_updates]);
        
            if ($hasil->isOk()) {
                return response([
                    'status' => true,
                    'message' => 'berhasil',
                    'data' => $hasil->getDescription(),
                ]);
            }
        } catch (TelegramException $e) {
            // log telegram errors
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ]);
        }
        
    }
   
    public function getUpdateDatabase()
    {
        //    $hasil = DB::table('bot_flag')->get();
       $this->telegram->enableMySql([
        'host'     => '127.0.0.1',
        'port'     => 3306, // optional
        'user'     => 'root',
        'password' => '',
        'database' => 'mahasiswa',
       ]);

       return response([
           'status' => true,
           'message' => 'berhasil mendapatkan data',
           'data' => $this->telegram->handleGetUpdates(),
       ], 200);
    }

    public function getUpdate()
    {
        $this->telegram->useGetUpdatesWithoutDatabase();
        $allowed_updates = [
            Update::TYPE_MESSAGE,
            Update::TYPE_CHANNEL_POST,
            // etc.
        ];
        return response([
            'status' => true,
            'message' => 'berhasil',
            'data' => $this->telegram->handleGetUpdates(['allowed_updates' => $allowed_updates]),
        ], 200);
    }

    public function sendMessage(Request $request)
    {
        $pesan = $request->get('pesan');

        $hasil = RequestTelegram::sendMessage([
            'chat_id' => '734229344',
            'text' => $pesan,
        ]);

        print_r($hasil);

        echo $hasil->result->chat['id'];

        // return response($hasil, 200);
    }

    public function deleteWebhook()
    {
        $hasil = $this->telegram->deleteWebhook();
        return response([
            'status' => true,
            'message' => 'berhasil',
            'data' => $hasil->getDescription(),
        ]);
    }

    public function deleteMessage(Request $request)
    {
        $message_id = $request->get('message_id');
        $chat_id = $request->get('chat_id');

        $hapus = RequestTelegram::deleteMessage([
            'message_id' => $message_id,
            'chat_id' => $chat_id,
        ]);

        return response([
            'status' => true,
            'message' => 'berhasil',
            'data' => $hapus,
        ], 200);
    }
    
}
