<?php

/**
 * @package Управление пользователями
 * @version 1.0.0
 * @author  Botify <hello@botify.ru>
 * @link    https://botify.ru
 */

$bot->isAdmin(function() use($bot) {
    $bot->hear('/^!unban/iu', function() use($bot) {
        [$cmd, $user_id] = $bot->parse();

        if (!is_numeric($user_id))
            return $bot->say('*Ошибка:* Неправильный формат ID пользователя.');

        if (!$bot->user->exist($user_id))
            return $bot->say('*Ошибка:* Пользователь с таким ID не найден.');

        $bot->user->unBanById($user_id);

        $username = $bot->user->getById($user_id, ['full_name'])['full_name'];

        $bot->say("*Пользователь $username ($user_id) разблокирован.*");
        $bot->sendMessage($user_id, "*Вы были разблокированы.*");
    });

    $bot->hear('/^!ban/iu', function() use($bot) {
        [$cmd, $user_id, $time, $time_name, $comment] = $bot->parse();

        if (!is_numeric($user_id)) {
            return $bot->say('*Ошибка:* Неправильный формат ID пользователя.');
        }

        if (!$bot->user->exist($user_id))
            return $bot->say('*Ошибка:* Пользователь с таким ID не найден.');

        $comment = implode(' ', explode('_', $comment));

        $ban_start = time();
        $ban_end = false;

        if (stripos($time_name, 'сек') !== false) {
            $ban_end = strtotime("+$time sec");
        }

        if (stripos($time_name, 'мин') !== false) {
            $ban_end = strtotime("+$time minutes");
        }

        if (stripos($time_name, 'час') !== false) {
            $ban_end = strtotime("+$time hour");
        }

        if (stripos($time_name, 'ден') !== false || stripos($time_name, 'дне') !== false || stripos($time_name, 'дня') !== false) {
            $ban_end = strtotime("+$time hour");
        }

        if (stripos($time_name, 'меся') !== false) {
            $ban_end = strtotime("+$time month");
        }

        if (stripos($time_name, 'год') !== false || stripos($time_name, 'лет') !== false) {
            $ban_end = strtotime("+$time year");
        }

        if (!$ban_end or !is_numeric($time))
            return $bot->say('*Ошибка:* Указано некорректное время.');

        $bot->user->banById($user_id, $comment, $ban_start, $ban_end);
        // $bot->user->clearStateById($user_id); // сбрасываем стейт юзера если был

        $username = $bot->user->getById($user_id, ['full_name'])['full_name'];

        $msg  = "*Пользователь $username ($user_id) заблокирован.*\n";
        $msg .= "Блокировка с `" . date('d.m.Y H:i:s', $ban_start) . "`, по `" . date('d.m.Y H:i:s', $ban_end) . "`\n";
        $msg .= "Комментарий: _$comment _";
        $bot->say($msg);

        $msg  = "*Вы были заблокированы.*\n";
        $msg .= "Блокировка с `" . date('d.m.Y H:i:s', $ban_start) . "`, по `" . date('d.m.Y H:i:s', $ban_end) . "`\n";
        $msg .= "Комментарий: _$comment _";
        $bot->sendMessage($user_id, $msg);
    });

    $bot->hear('/^!id/iu', function() use($bot) {
        [$cmd, $id] = $bot->parse();

        if (!is_numeric($id))
            return $bot->say('*Ошибка:* Неправильный формат ID пользователя.');

        $user = $bot->db->table('users')->find($id, ['user_id', 'full_name', 'username']);

        if (sizeof($user) == 0) {
            return $bot->reply("Пользователь с таким ID не найден.");
        }

        $username = '@' . $user['username'] ?? '';
        $bot->reply("ID: $c `{$user['user_id']}` ([{$user['full_name']}](tg://user?id={$user['user_id']})) $username");
    });

    $bot->hear('/^!list/iu', function() use($bot) {
        $list = $bot->db->table('users')->where('ban', '1')->get();
        $msg = '';
        if (sizeof($list) == 0) {
            $msg = "Список пуст.";
        } else {
            foreach($list as $key => $user) {
                $index = $key + 1;
                $msg = "$index. [{$user['full_name']}](tg://user?id={$user['user_id']}) `{$user['user_id']}`\n";
            }
        }
        $bot->reply($msg);
    });
});
