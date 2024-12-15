#!/usr/
<?php

require_once dirname(__FILE__) . '/../core/class/lgthinq2.class.php';

log::add('lgthinq2', 'info', __('DÉMON MQTT : ', __FILE__) . __('Activation du service MQTT LGThinQ', __FILE__));

lgthinq2::getTokenIsExpired();

$fileCertAmazonCA = dirname(__FILE__) . '/../../../data/AmazonRootCA1.pem';
$fileCertClient = dirname(__FILE__) . '/../../../data/certificatePem.pem';
$fileCertClientKey = dirname(__FILE__) . '/../../../data/pass.pem';

try {

    // GET LG MQTT SERVER
    $headers = lgthinq2::defaultGwHeaders();
    $curlMqtt = curl_init();
    curl_setopt_array($curlMqtt, array(
        CURLOPT_URL => lgthinq2::LGTHINQ_MQTT_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers
    ));
    $rep = curl_exec($curlMqtt);
    curl_close($curlMqtt);
    $gatewayRes = json_decode($rep, true);
    if (!$gatewayRes || !isset($gatewayRes['result']) || $gatewayRes['resultCode'] != '0000') {
        log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('a planté ', __FILE__) . json_encode($gatewayRes));
        return;
    }
    if ($gatewayRes['resultCode'] != '0000') {
        if ($gatewayRes['resultCode'] == '0110') {
            event::add('jeedom::alert', array(
                'level' => 'success',
                'page' => 'lgthinq2',
                'message' => __("Les conditions générales ont changées, merci de vous rendre sur l'applications pour les accepter.", __FILE__),
            ));
        }
        log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('a planté ', __FILE__) . json_encode($gatewayRes));
        return;
    }
    log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('API SERVER ', __FILE__) . $gatewayRes['result']['apiServer']);
    //config::save('LGE_API_SERVER', $gatewayRes['result']['apiServer'], 'lgthinq2');
    //config::save('LG_MQTT_SERVER', $gatewayRes['result']['mqttServer'], 'lgthinq2');

    // GET PRIVATE KEY
    $curlAzu = curl_init();
    curl_setopt_array($curlAzu, array(
        CURLOPT_URL => lgthinq2::LGTHINQ_MQTT_AZU,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET'
    ));
    $repAzu = curl_exec($curlAzu);
    curl_close($curlAzu);
    $azuRes = json_decode($repAzu, true);
    if (!$azuRes || !isset($azuRes['privKey'])) {
        log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('a planté ', __FILE__) . json_encode($azuRes));
        return;
    }
    log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('AZU KEYS ', __FILE__) . $azuRes['privKey']);
    //config::save('MQTT_AZU_PRIVKEY', $azuRes['privKey'], 'lgthinq2');
    //config::save('MQTT_AZU_CSR', $azuRes['csr'], 'lgthinq2');
    file_put_contents($fileCertClientKey, $azuRes['privKey']);
    shell_exec('sudo chmod 755 ' . $fileCertClientKey);

    // GET CLIENT INIT REQUEST
    $repCli = lgthinq2::postData(lgthinq2::LGTHINQ2_SERV_URL . 'service/users/client', '', lgthinq2::defaultDevicesHeaders());
    log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('CLIENT ', __FILE__) . $repCli);
    $cliRes = json_decode($repCli, true);
    if (!$cliRes || !isset($cliRes['resultCode']) || $cliRes['resultCode'] == '0102') {
        lgthinq2::login();
    }
    if ((!$cliRes || !isset($cliRes['resultCode']) || $cliRes['resultCode'] == '0110') && config::byKey('authorize_terms', 'lgthinq2', false) == true) {
        lgthinq2::terms();
    }

    // GET PRIVATE CLIENT CERTIFICATE
    $csr = str_replace(array("-----BEGIN CERTIFICATE REQUEST-----","-----END CERTIFICATE REQUEST-----"), '', str_ireplace(array("\r","\n",'\r','\n'),'',$azuRes['csr']));
    //log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('CSR ', __FILE__) . $csr);
    $repCer = lgthinq2::postData(lgthinq2::LGTHINQ2_SERV_URL . 'service/users/client/certificate', json_encode(array('csr' => $csr)), lgthinq2::defaultDevicesHeaders());
    $cerRes = json_decode($repCer, true);
    if (!$cerRes || !isset($cerRes['result']) || $cerRes['resultCode'] != '0000') {
        log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('a planté ', __FILE__) . json_encode($cerRes));
        return;
    }
    log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('CER ', __FILE__) . json_encode($cerRes));
    //config::save('MQTT_CER_PEM', $cerRes['result']['certificatePem'], 'lgthinq2');
    file_put_contents($fileCertClient, $cerRes['result']['certificatePem']);
    shell_exec('sudo chmod 777 ' . $fileCertClient);
    //config::save('MQTT_SUB', $cerRes['result']['subscriptions'][0], 'lgthinq2');

    // GET AMAZON ROOT CA
    if (!is_file($fileCertAmazonCA)) {
        shell_exec('sudo wget --quiet -O ' . $fileCertAmazonCA . ' ' . lgthinq2::LGTHINQ_MQTT_CER);
        log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('CERTCA ', __FILE__) .  $fileCertAmazonCA);
    }

    // INIT MQTT CONNECTION
    $mqttUri = parse_url($gatewayRes['result']['mqttServer']);
    $mqtt = new \PhpMqtt\Client\MqttClient(str_replace('.iot.', '-ats.iot.', $mqttUri['host']), $mqttUri['port'], lgthinq2::getClientId());

    $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
        ->setKeepAliveInterval(60)
        ->setConnectTimeout(10)
        ->setReconnectAutomatically(true)
        ->setUseTls(true)
        ->setTlsCertificateAuthorityFile($fileCertAmazonCA)
        ->setTlsClientCertificateFile($fileCertClient)
        ->setTlsClientCertificateKeyFile($fileCertClientKey);

    $mqtt->connect($connectionSettings, false);
    /*$mqtt->registerLoopEventHandler(function (\PhpMqtt\Client\MqttClient $mqtt, float $elapsedTime) {
        if ($elapsedTime >= 10) {
            log::add('lgthinq2', 'debug', __('DÉMON MQTT : ', __FILE__) . __('LOOP interrupted ', __FILE__));
            $mqtt->reconnect();
        }

    });*/
    log::add('lgthinq2', 'info', __('DÉMON MQTT : ', __FILE__) . __('connecté', __FILE__));
    foreach ($cerRes['result']['subscriptions'] as $subscription) {
        $mqtt->subscribe($subscription, function (string $topic, string $message, bool $retained) use ($mqtt) {
            $json = json_decode($message, true);
            foreach (eqLogic::byType('lgthinq2') as $lgthinq2) {
                if ($lgthinq2->getLogicalId() == $json['deviceId']) {
                    $timestamp = null;
                    if (isset($json['data']['state']['reported'])) {
                        $deviceTypeConfigFile = lgthinq2::loadConfigFile($json['deviceId']);
                        if (!is_object($lgthinq2->getCmd('info', 'online'))) {
                            $lgthinq2->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, 'online');
                        }
                        if (isset($json['data']['state']['reported']['timestamp'])) {
                            $timestamp = date('Y-m-d H:i:s', ($json['data']['state']['reported']['timestamp']/1000));
                        }
                        $lgthinq2->checkAndUpdateCmd('online', $json['data']['state']['reported']['online'], $timestamp);
                        $refState = lgthinq2::deviceTypeConstantsState($lgthinq2->getConfiguration('deviceType'));
                        if ($refState) {
                            $data = $json['data']['state']['reported'][$refState];
                        } else {
                            $data = $json['data']['state']['reported'];
                        }
                        foreach ($data as $refStateId => $refStateValue) {
                            if (!is_object($lgthinq2->getCmd('info', $refStateId))) {
                                $lgthinq2->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $refStateId);
                            }
                            $lgthinq2->checkValueAndUpdateCmd($refStateId, $refStateValue, $timestamp);
                            log::add('lgthinq2', 'info', __('DÉMON MQTT : ', __FILE__) . $lgthinq2->getName() . __(' commande mise à jour : ', __FILE__) . $refStateId . __(' à la valeur : ', __FILE__) .$refStateValue . __(' au temps : ', __FILE__) . $timestamp);
                        }
                    }
                }
            }

            $mqtt->reconnect();
        }, 1);
    }

    $mqtt->loop(true, true, 60);

    $mqtt->disconnect();

} catch (\PhpMqtt\Client\MqttClientException $e) {
    log::add('lgthinq2', 'info', __('DÉMON MQTT : ', __FILE__) . __('Erreur rencontrée ', __FILE__). json_encode(utils::o2a($e)));
    lgthinq2::deamon_start();
}

?>
