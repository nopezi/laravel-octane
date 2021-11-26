<?php

namespace App\Http\Controllers\bot\telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramBotModel as TelegramModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Longman\TelegramBot\Request as RequestTelegram;

class Sholat extends Controller
{
    
    
    public function __construct()
    {
        // parent::__construct();
        $telegram_model = new TelegramModel();
        $this->telegram_model = $telegram_model;
    }
    
    public function main($terima)
    {
        $flag = $this->telegram_model->getUserVar('FLAG', $terima['USER_ID']);
        $cek  = $this->pilihKota($flag, $terima);
        $cek .= $this->jadwalSholat($flag, $terima);
        return $cek;
    }

    private function pilihKota($flag, $terima)
    {

        if (
            empty($flag) && (strstr($terima['PESAN'], 'jadwal sholat') 
            || $terima['PESAN'] == '/jadwal_sholat')
        ) {

            $this->telegram_model->setUserVar('FLAG', 'jadwal_sholat', $terima['USER_ID']);
            $kota = ['Jakarta', 'Bandung', 'Yogyakarta'];
            $pesan = '';

            for ($i=0; $i < sizeof($kota); $i++) { 
                $pesan .= $i+1 . ' ' . $kota[$i] . "\n";
            }

            RequestTelegram::sendMessage([
                'chat_id' => $terima['USER_ID'],
                'text' => $pesan,
            ]);

            $hasil_kirim = RequestTelegram::sendMessage([
                'chat_id' => $terima['USER_ID'],
                'text' => "Silahkan pilih salah satu kota di atas",
            ]);

            // log pesan terima dari user ke bot
            $this->telegram_model->botLogTerima($terima);
            // log pesan terkirim dari bot ke user
            $this->telegram_model->botLogKirim($hasil_kirim);

            return true;

        }
        
    }

    private function jadwalSholat($flag, $terima)
    {
        $client = new Client();

        if (!empty($flag) && $flag == 'jadwal_sholat') {
            
            $listKota = ['Jakarta', 'Bandung', 'Yogyakarta'];

            for ($i=0; $i < sizeof($listKota); $i++) { 
                if (PESAN == $i+1 || strtolower(PESAN == $listKota[$i])) {
                    $kota = $listKota[$i];
                }
            }

            if (!empty($kota)) { 
                
                $url = "https://muslimsalat.com/{$kota}.json";

                $hasil = $client->request('GET', $url, ['query' => [
                    'key' => '0c627b9ed0be4489d77253a84ec91876',
                ]]);

                $data = json_decode($hasil->getBody(), true);

                // print_r($data);

                if ($data['status_code'] == 1) {
                    $pesan = "jadwal sholat hari ini tanggal {$data['items'][0]['date_for']} \n\n";
                    $pesan .= "Fajar : {$data['items'][0]['fajr']} \n";
                    $pesan .= "Subuh : {$data['items'][0]['shurooq']} \n";
                    $pesan .= "Zuhur : {$data['items'][0]['dhuhr']} \n";
                    $pesan .= "Asar : {$data['items'][0]['asr']} \n";
                    $pesan .= "Maghrib : {$data['items'][0]['maghrib']} \n";
                    $pesan .= "Isya : {$data['items'][0]['isha']} \n";
                } else {
                    $pesan = "Maaf jadwal sholat tidak di temukan $kota";
                }

                $this->telegram_model->setUserVar('FLAG', '', $terima['USER_ID']);

                $hasil_kirim = RequestTelegram::sendMessage([
                    'chat_id' => USER_ID,
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
    
}
