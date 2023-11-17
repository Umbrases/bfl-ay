<?php

require_once (__DIR__ .'/crest.php');
require_once (__DIR__ .'/getQuery.php');
set_time_limit(0);

$deals = getQuery('crm.deal.list', [ //Получение списка сделок
    'filter' => [ "CATEGORY_ID" =>  58, "CLOSED" => "N"],
    'select' => [  "ID", "CONTACT_ID", "TITLE", "CATEGORY_ID", "UF_CRM_1656395923", "UF_CRM_1656395958", "UF_CRM_1656395994", "UF_CRM_1698743447" ]
]);


$arDeals = $deals['result'];
if( $deals['total'] > 50 ){
    $i = 50;
    while( $i < $deals['total'] ){
        $res_x = getQuery('crm.deal.list', [ //Получение списка сделок
            'filter' => [ "CATEGORY_ID" =>  58, "CLOSED" => "N"],
            'select' => [  "ID", "CONTACT_ID",  "TITLE", "CATEGORY_ID", "UF_CRM_1656395923", "UF_CRM_1656395958", "UF_CRM_1656395994", "UF_CRM_1698743447" ],
            'start'=>$i
        ]);
        $arDeals = array_merge($arDeals,$res_x['result']);
        $i = $i + 50;
    }
}

foreach ($arDeals as $deal) { // перебор массива сделок
    if (empty($deal["UF_CRM_1656395923"]) || empty($deal["UF_CRM_1656395958"]) || empty($deal["UF_CRM_1656395994"]) || empty($deal["UF_CRM_1698743447"])){
        $deal_fields = getQuery('crm.deal.list', [ //Получение списка сделок
            'filter' => [ "CONTACT_ID" =>  $deal["CONTACT_ID"]],
            'select' => [  "ID", "TITLE", "CATEGORY_ID", "UF_CRM_1656395923", "UF_CRM_1656395958", "UF_CRM_1656395994", "UF_CRM_1698743447", "CONTACT_ID" ],
        ]);
    }
    if ($deal_fields['total'] > 1) {
        foreach ($deal_fields as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item => $sd) {
                        if (is_array($sd)) {
                            if($sd["CATEGORY_ID"] == "4") {
                                if (!empty($sd["UF_CRM_1656395923"]) && !empty($sd["UF_CRM_1656395958"]) && !empty($sd["UF_CRM_1656395994"]) || !empty($sd["UF_CRM_1698743447"])) {
                                    if (empty($sd["UF_CRM_1698743447"])) {
                                        $deal_bfl_update = getQuery('crm.deal.update', [
                                            'id' => $sd["ID"],
                                            'fields' => [
                                                    'UF_CRM_1698743447' => $deal["ID"],
                                                ],
                                        ]);

                                        $deal_sales = getQuery('crm.deal.get', [
                                            'id' => $sd["UF_CRM_1656395923"],
                                            'select' => [  "ID","UF_CRM_1698743447"],
                                        ]);
                                        if (empty($deal_sales['result']['UF_CRM_1698743447'])){
                                            $deal_sales_update = getQuery('crm.deal.update', [
                                                'id' => $deal_sales['result']["ID"],
                                                'fields' => [
                                                    'UF_CRM_1698743447' => $deal["ID"],
                                                ],
                                            ]);
                                        }

                                        $deal_payment = getQuery('crm.deal.get', [
                                            'id' => $sd["UF_CRM_1656395994"],
                                            'select' => [  "ID", "UF_CRM_1698743447"],
                                        ]);

                                        if (empty($deal_payment['result']['UF_CRM_1698743447'])){
                                            $deal_payment_update = getQuery('crm.deal.update', [
                                                'id' => $deal_payment['result']["ID"],
                                                'fields' => [
                                                    'UF_CRM_1698743447' => $deal["ID"],
                                                ],
                                            ]);
                                        }

                                        $deal_update =  getQuery('crm.deal.update', [
                                            'id' => $deal["ID"],
                                            'fields' => [
                                                'UF_CRM_1656395923' => $sd["UF_CRM_1656395923"],
                                                'UF_CRM_1656395958' => $sd["UF_CRM_1656395958"],
                                                'UF_CRM_1656395994' => $sd["UF_CRM_1656395994"],
                                                'UF_CRM_1698743447' => $deal["ID"],
                                            ],
                                        ]);
                                    }
                                }
                            }
                        }
                }
            }
        }
    }
}

?>
