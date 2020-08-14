<?php

class template_ui
{
    public function new_message()
    {
        global $STD;
        
        $new_message = '';
        
        // Do we need to dispatch messages? (I swear this is not the best place for this)
        if ($STD->userobj->data['disp_msg'] == 1) {
            $STD->userobj->data['disp_msg'] = 0;
            $STD->userobj->update();
            
            require_once ROOT_PATH.'lib/message.php';
            $MSG = new message;
            $MSG->query_order('mid', 'DESC');
            $MSG->query_limit('0', '1');
            $MSG->query_condition("receiver = '{$STD->user['uid']}' AND owner = '{$STD->user['uid']}'");
            $MSG->getAll();
            if ($MSG->nextItem()) {
                $msg = "<a href='{$STD->tags['root_url']}act=msg&amp;param=02&amp;mid={$MSG->data['mid']}'>{$MSG->data['title']}</a>";
            }

            $new_message = $STD->global_template->new_messages($msg);
        }
        
        return $new_message;
    }
}
