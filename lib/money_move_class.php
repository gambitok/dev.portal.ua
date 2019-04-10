<?php

class money_move {

    function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function show_money_move_list(){$db=DbSingleton::getDb();$where="";$limit ="limit 0,300"; if ($where!=""){$limit="";}
        $r=$db->query("select j.*, CASH.name as cash_name, pf.name as name_from, pf.full_name as full_name_from, muf.name as user_name_from,
        pt.name as name_to, pt.full_name as full_name_to, mut.name as user_name_to, msm.mcaption as status_move_name 
        from J_MONEY_MOVE j
            left outer join CASH on CASH.id=j.cash_id
            left outer join PAY_BOX pf on pf.id=j.paybox_id_from
            left outer join media_users muf on muf.id=j.user_id_from
            left outer join PAY_BOX pt on pt.id=j.paybox_id_to
            left outer join media_users mut on mut.id=j.user_id_to
            left outer join manual msm on msm.id=j.status_money_move
        where j.status=1 $where order by j.id desc $limit;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $data=$db->result($r,$i-1,"data");
            $data_accept=$db->result($r,$i-1,"data_accept"); if ($data_accept=="0000-00-00 00:00:00"){$data_accept="";}
            $cash_name=$db->result($r,$i-1,"cash_name");
            $summ=$db->result($r,$i-1,"summ");
            $paybox_name_from=$db->result($r,$i-1,"name_from");if ($paybox_name_from==""){$paybox_name_from=$db->result($r,$i-1,"full_name_from");}
            $user_name_from=$db->result($r,$i-1,"user_name_from");
            $paybox_name_to=$db->result($r,$i-1,"name_to");if ($paybox_name_to==""){$paybox_name_to=$db->result($r,$i-1,"full_name_to");}
            $user_name_to=$db->result($r,$i-1,"user_name_to");
            $status_move_name=$db->result($r,$i-1,"status_move_name");

            if ($data!=null) {
                $data = strtotime($data);
                $date_convert=date('d-m-Y H:i:s', $data);
            } else $date_convert=$data;

            if ($data_accept!=null) {
                $data_accept = strtotime($data_accept);
                $date_accept_convert=date('d-m-Y H:i:s', $data_accept);
            } else $date_accept_convert=$data_accept;

            $function="viewMoneyMove(\"$id\")";
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td align='center'>ПГ-$id</td>
                <td align='center'>$status_move_name</td>
                <td align='center'>$date_convert</td>
                <td align='center'>$cash_name</td>
                <td align='center'>$summ</td>
                <td>$paybox_name_from/$user_name_from</td>
                <td>$paybox_name_to/$user_name_to</td>
                <td>$date_accept_convert</td>
            </tr>";
        }
        return $list;
    }

    function loadMoneyMoveCashBoxList($client_id,$doc_type_id,$seller_id){
        $list=$this->showPayBoxSelectList(0,$doc_type_id,$seller_id);
        return $list;
    }

    function showMoneyMoveForm(){session_start();$user_id=$_SESSION["media_user_id"];
        $form="";$form_htm=RD."/tpl/money_move_form.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{user_id}",$user_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{paybox_from_list}",$this->showPayBoxUserSelectList($user_id),$form);
        return $form;
    }

    function viewMoneyMove($move_id){$db=DbSingleton::getDb();$slave=new slave;
        $form="";$form_htm=RD."/tpl/money_move_accept_view.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select j.*, CASH.name as cash_name, pf.name as name_from, pf.full_name as full_name_from, muf.name as user_name_from,
        pt.name as name_to, pt.full_name as full_name_to, mut.name as user_name_to from J_MONEY_MOVE j
            left outer join CASH on CASH.id=j.cash_id
            left outer join PAY_BOX pf on pf.id=j.paybox_id_from
            left outer join media_users muf on muf.id=j.user_id_from
            left outer join PAY_BOX pt on pt.id=j.paybox_id_to
            left outer join media_users mut on mut.id=j.user_id_to
        where j.id='$move_id' limit 0,1;");
        $data=$db->result($r,0,"data");
        $cash_name=$db->result($r,0,"cash_name");
        $summ=$db->result($r,0,"summ");
        $status_money_move=$db->result($r,0,"status_money_move");
        $dis_accept=""; if ($status_money_move==126){$dis_accept="disabled";}
        $paybox_name_from=$db->result($r,0,"name_from");if ($paybox_name_from==""){$paybox_name_from=$db->result($r,0,"full_name_from");}
        $user_name_from=$db->result($r,0,"user_name_from");
        $paybox_name_to=$db->result($r,0,"name_to");if ($paybox_name_to==""){$paybox_name_to=$db->result($r,0,"full_name_to");}
        $user_name_to=$db->result($r,0,"user_name_to");

        if ($move_id==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        $form=str_replace("{move_id}",$move_id,$form);
        $form=str_replace("{data}",$slave->data_word($data),$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{summ}",$summ,$form);
        $form=str_replace("{dis_accept}",$dis_accept,$form);
        $form=str_replace("{paybox_name_from}",$paybox_name_from,$form);
        $form=str_replace("{user_name_from}",$user_name_from,$form);
        $form=str_replace("{paybox_name_to}",$paybox_name_to,$form);
        $form=str_replace("{user_name_to}",$user_name_to,$form);
        return $form;
    }

    function showPayBoxSelectList($paybox_id,$doc_type_id,$seller_id){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];$list="";
        $where_seller="";if ($seller_id>0){$where_seller=" and pb.firm_id='$seller_id'";}
        $r=$db->query("select pb.* from PAY_BOX pb 
            left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id 
        where pbw.worker_id='$user_id' and pb.doc_type_id='$doc_type_id' $where_seller and pbw.status=1 and pb.status=1 and pb.in_use=1 order by pb.name asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'";if ($i==1){$list.=" selected";}$list.=">$name</option>";
        }
        return $list;
    }

    function showPayBoxUserSelectList($user_id,$paybox_id=null){$db=DbSingleton::getDb(); $list="";
        if ($user_id=="" || $user_id==0){session_start();$user_id=$_SESSION["media_user_id"];}
        $r=$db->query("select pb.* from PAY_BOX pb 
            left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id 
            left outer join B_PAYBOX_BALANS pbb on pbb.paybox_id=pb.id 
        where pbw.worker_id='$user_id' and pbb.user_id='$user_id' and pbb.saldo>0 and pbw.status=1 and pb.status=1 and pb.in_use=1 order by pb.name asc;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'";if ($id==$paybox_id){$list.=" selected";}$list.=">$name</option>";
        }
        return $list;
    }

    function getPayboxUserCashSaldoList($paybox_id,$user_id){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
        if ($user_id>0 || $paybox_id>0 ){
            $r=$db->query("select pb.* from B_PAYBOX_BALANS pb where pb.user_id='$user_id' and pb.paybox_id='$paybox_id' order by pb.cash_id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $saldo=$db->result($r,$i-1,"saldo");
                $cash_id=$db->result($r,$i-1,"cash_id");$cash_name=$this->getCashAbr($cash_id);
                $list.="<option value='$id' max-saldo='$saldo'>$saldo $cash_name</option>";
            }
        }
        return $list;
    }

    function getPayboxResiverList($paybox_id,$balans_id_from,$user_id){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
        $paybox_type_id=$this->getPayBoxType($paybox_id);
        $r=$db->query("select pb.* from PAY_BOX pb 
            left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id 
        where pb.doc_type_id='$paybox_type_id' and pbw.status=1 and pb.status=1 and pb.in_use=1 group by pb.id order by pb.name asc;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'>$name</option>";
        }
        return $list;
    }

    function getPayboxManagerList($paybox_id,$balans_id_from){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
        $paybox_type_id=$this->getPayBoxType($paybox_id);
        $r=$db->query("select pbw.* from PAY_BOX pb 
            left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id 
        where pb.id='$paybox_id' and pb.doc_type_id='$paybox_type_id' and pbw.status=1 and pb.status=1 and pb.in_use=1 group by pbw.worker_id order by pb.name asc;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $worker_id=$db->result($r,$i-1,"worker_id");
            $worker_name=$this->getMediaUserName($worker_id);
            $list.="<option value='$worker_id'>$worker_name</option>";
        }
        return $list;
    }

    function getPayBoxType($paybox_id){$db=DbSingleton::getDb(); $type_id=0;
        $r=$db->query("select doc_type_id from PAY_BOX where id='$paybox_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$type_id=$db->result($r,0,"doc_type_id");}
        return $type_id;
    }

    function getPayBoxUserBalans($paybox_id,$user_id,$cash_id){$db=DbSingleton::getDb(); $saldo=0;
        $r=$db->query("select saldo from B_PAYBOX_BALANS where user_id='$user_id' and paybox_id='$paybox_id' and cash_id='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
        }
        return $saldo;
    }

    function getPayBoxBalans($paybox_id){$db=DbSingleton::getDb(); session_start();$user_id=$_SESSION["media_user_id"];$list="---";
        $r=$db->query("select pb.* from B_PAYBOX_BALANS pb where pb.user_id='$user_id' and pb.paybox_id='$paybox_id' order by pb.id asc;");$n=$db->num_rows($r);
        if ($n>0){$list="";
            for ($i=1;$i<=$n;$i++){
                $saldo=$db->result($r,$i-1,"saldo");
                $cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
                $last_update=$db->result($r,$i-1,"last_update");
                $list.="<strong>$saldo $cash_abr</strong>-$last_update<br>";
            }
        }
        return $list;
    }

    function getCashAbr($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select abr from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function getPayBoxUserBalansById($id){$db=DbSingleton::getDb();$saldo=$cash_id=$user_id_from=0;
        $r=$db->query("select * from B_PAYBOX_BALANS where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
            $cash_id=$db->result($r,0,"cash_id");
            $user_id_from=$db->result($r,0,"user_id");
        }
        return array($saldo,$cash_id,$user_id_from);
    }

    function saveMoneyMove($paybox_id_from,$paybox_id_to,$user_id_to,$balans_id_from,$summ){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $paybox_id_from=$slave->qq($paybox_id_from);$paybox_id_to=$slave->qq($paybox_id_to);$user_id_to=$slave->qq($user_id_to);$move_id=0;
        $balans_id_from=$slave->qq($balans_id_from);$summ=$slave->qq($summ);
        if ($paybox_id_from>0 && $paybox_id_to>0 && $user_id_to>0 && $balans_id_from>0 && $summ>=0){
            list($current_balans_summ,$cash_id,$user_id_from)=$this->getPayBoxUserBalansById($balans_id_from);
            if ($summ>$current_balans_summ){ $answer=0;$err="Сума переміщення вже більша за наявну у касі!"; }
            if ($summ<=$current_balans_summ){
                $r=$db->query("select max(id) as mid from J_MONEY_MOVE;"); $move_id=$db->result($r,0,"mid")+1;
                $db->query("insert into J_MONEY_MOVE (`id`,`paybox_id_from`,`user_id_from`,`paybox_id_to`,`user_id_to`,`cash_id`,`summ`) value ('$move_id','$paybox_id_from','$user_id_from','$paybox_id_to','$user_id_to','$cash_id','$summ');");
                $this->updatePayboxBalans($paybox_id_from,2,$cash_id,$summ,$user_id_from,$move_id);
                //$this->updatePayboxBalans($paybox_id_to,1,$cash_id,$summ,$user_id_to,$move_id);
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$move_id);
    }

    function acceptMoneyMove($move_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Прийняти переказ може лише адресат!";
        $move_id=$slave->qq($move_id);
        if ($move_id>0){
            $r=$db->query("select * from J_MONEY_MOVE where id='$move_id' and status='1' and status_money_move='125' and user_id_to='$user_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $paybox_id_to=$db->result($r,0,"paybox_id_to");
                $user_id_to=$db->result($r,0,"user_id_to");
                $cash_id=$db->result($r,0,"cash_id");
                $summ=$db->result($r,0,"summ");
                $data_accept=date("Y-m-d H:i:s");
                $db->query("update J_MONEY_MOVE set data_accept='$data_accept', status_money_move='126' where id='$move_id';");
                $this->updatePayboxBalans($paybox_id_to,1,$cash_id,$summ,$user_id_to,$move_id);
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$move_id);
    }

    function updatePayboxBalans($paybox_id,$deb_kre,$cash_id,$summ,$user_id,$money_move_id){$db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kol from B_PAYBOX_BALANS where paybox_id='$paybox_id' and cash_id='$cash_id' and user_id='$user_id';");$ex=$db->result($r,0,"kol"); if ($deb_kre==2){ $summ=$summ*-1; }
        if ($ex==0){
            $db->query("insert into B_PAYBOX_BALANS (`paybox_id`,`saldo`,`cash_id`,`user_id`) values ('$paybox_id','$summ','$cash_id','$user_id');");
        }
        if ($ex>0){
            $db->query("update B_PAYBOX_BALANS set saldo=saldo+$summ where `paybox_id`='$paybox_id' and `cash_id`='$cash_id' and `user_id`='$user_id' limit 1;");
        }
        // insert paybox journal record
        $r=$db->query("select * from B_PAYBOX_JOURNAL where paybox_id='$paybox_id' and cash_id='$cash_id' and user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r); $saldo_before=0;
        if ($n==1){
            $saldo_before=$db->result($r,0,"saldo_after");
        }
        $sald_after=round($saldo_before+$summ,2);
        $db->query("insert into B_PAYBOX_JOURNAL (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`) values ('$paybox_id','$user_id','$saldo_before','$summ','$sald_after','$cash_id','$money_move_id');");
        return;
    }

}