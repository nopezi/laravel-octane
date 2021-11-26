<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TelegramBotModel extends Model
{

    public function botLogTerima($terima)
    {
        
        DB::table('bot_log')->insert([
            'message_id' => $terima['MESSAGE_ID'],
            'user_id' => $terima['USER_ID'],
            'first_name_user' => $terima['FIRST_NAME'],
            'last_name_user' => $terima['LAST_NAME'],
            'message' => $terima['PESAN'],
            'jenis_kirim' => 'terima',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
    }

    public function botLogKirim($data)
    {

        $message_id_kirim = $data->result->message_id;
        $user_id_kirim = $data->result->chat['id'];
        $first_name_kirim = $data->result->from['first_name'];
        $message_kirim = $data->result->text;
        $username_kirim = $data->result->from['username'];
        
        DB::table('bot_log')->insert([
            'message_id' => $message_id_kirim,
            'user_id' => $user_id_kirim,
            'first_name_user' => $first_name_kirim,
            'last_name_user' => $username_kirim,
            'message' => $message_kirim,
            'jenis_kirim' => 'kirim',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
    }

    public function setUserVar($flag, $data, $user_id)
    {
        $cek = DB::table('bot_flag')
                ->where('user_id', '=', $user_id)
                ->where('flag', '=', $flag)
                ->first();
        // jika ada maka update
        if (!empty($cek)) {
            DB::table('bot_flag')
                ->where('id', '=', $cek->id)
                ->update([
                    'data' => $data,
                ]);
        } 
        // jika tidak ada maka input baru
        else {
            DB::table('bot_flag')->insert([
                'user_id' => $user_id,
                'flag' => $flag,
                'data' => $data,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function getUserVar($flag, $user_id)
    {
        $cek = DB::table('bot_flag')
                ->where('user_id', '=', $user_id)
                ->where('flag', '=', $flag)
                ->first();
        if (!empty($cek)) {
            return $cek->data;
        }
    }
    
}
