<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__ . "/../../../../core/php/core.inc.php";

class lgthinq2 extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '0.10';

    const LGTHINQ_GATEWAY      = 'https://route.lgthinq.com:46030/v1/service/application/gateway-uri';
    const LGTHINQ_GATEWAY_LIST = 'https://kic.lgthinq.com:46030/api/common/gatewayUriList';
    const LGE_MEMBERS_URL      = 'https://fr.lgemembers.com';
    const LGACC_SIGNIN_URL     = 'https://fr.lgemembers.com/lgacc/front/v1/signin/';
    const LGACC_SERVSIGNIN_URL = 'https://fr.lgemembers.com/lgacc/service/v1/signin';
    const LGAPI_DATETIME       = 'https://fr.lgeapi.com/datetime';
    const LG_EMPTERMS_URL      = 'https://fr.emp.lgsmartplatform.com/';
    const LGACC_SPX_URL        = 'https://fr.m.lgaccount.com/spx/';
    const LGTHINQ_SERV_DEVICES = 'https://eic-service.lgthinq.com:46030/v1/service/devices/';
  
    const APPLICATION_KEY      = '6V1V8H2BN5P9ZQGOI5DAQ92YZBDO3EK9'; // for spx login
    const OAUTHSECRETKEY       = 'c053c2a6ddeb7ad97cb0eed0dcb31cf8';
    const APPKEY               = 'LGAO221A02';
    const SVCCODE              = 'SVC202';
    const XAPIKEY              = 'VGhpblEyLjAgU0VSVklDRQ==';
    const MAXRETRY             = 3;
      
    public static function deviceTypeConstants($_id) {
        $_deviceTypes = array(
            101 => __('Réfrigérateur', __FILE__),
            201 => __('Lave-linge', __FILE__),
            202 => __('Sèche-linge', __FILE__),
            221 => __('Laveuse', __FILE__),
            222 => __('Sécheuse', __FILE__),
            204 => __('Lave-vaisselle', __FILE__),
            301 => __('Four', __FILE__)
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:$_id;
    }
  
    // Fonction pour effectuer une requête POST
    public static function postData($url, $data, $headers) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function doRetry($stepFunction, $rawContent = false) {
        $result = null;
        for ($i = 1; $i <= lgthinq2::MAXRETRY; $i++) {
            $result = $stepFunction();
            if (!$result) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . 'Étape a échoué, tentative ' . $i . '/' . lgthinq2::MAXRETRY);
            } else {
                if ($rawContent) {
                    $res = json_decode($result, true);
                    if ($res && isset($res['error']) && isset($res['error']['message'])) {
                        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 2 a échoué ' . $res['error']['message'] . ', tentative '.$i.'/' . lgthinq2::MAXRETRY);
                        sleep(2);
                    } else {
                        return $result;
                    }
                } else {
                    return $result;
                }
            }
        }
        return null;
    }
  
    public static function getClientId() {
         if (config::byKey('cliend_id', __CLASS__, '') == '') {
             log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Création du client_id ', __FILE__));
             config::save('cliend_id', bin2hex(random_bytes(32)), __CLASS__);
         }
         return config::byKey('cliend_id', __CLASS__);
    }
  
    public static function getLanguage($_type) {
        $lang = config::byKey('language', 'core', 'fr_FR');
        $arrLang = explode('_', $lang);
        switch ($_type) {
            case 'lowercase':
                return ($arrLang[0]?$arrLang[0]:strtolower($arrLang[1]));
            case 'uppercase':
                return ($arrLang[1]?$arrLang[1]:strtoupper($arrLang[0]));
            case 'hyphen':
                return str_replace('_', '-', $lang);
            case 'plain':
                return $lang;
            default:   
                return $lang;
        }
    }

    public static function oldDefaultHeaders() {
        return array(
            'Accept: */*',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            'X-Requested-With: XMLHttpRequest'
        );
    }

    public static function defaultHeaders() {
        return array(
            'Accept: application/json',
            'X-Application-Key: ' . lgthinq2::APPLICATION_KEY,
            'X-Client-App-Key: ' . lgthinq2::APPKEY,
            'X-Lge-Svccode: SVC709',
            'X-Device-Type: M01',
            'X-Device-Platform: ADR',
            'X-Device-Language-Type: IETF',
            'X-Device-Publish-Flag: Y',
            'X-Device-Country: ' . lgthinq2::getLanguage('uppercase'),
            'X-Device-Language: ' . str_replace('_', '-', config::byKey('language', 'core', 'fr_FR')),
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Access-Control-Allow-Origin: *',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen')  . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
        );
    }
  
    public static function defaultDevicesHeaders() {
        return array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ';q=1',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: LG ThinQ/4.1.49230 (iPhone; iOS 16.7; Scale/2.00)',
            'x-api-key: ' . lgthinq2::XAPIKEY,
            'x-app-version: 4.1.49230',
            'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
            'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
            'x-model-name: iPhone SE(2nd Gen)',
            'x-origin: app-native',
            'x-os-version: 16.7',
            'x-service-code: ' . lgthinq2::SVCCODE,
            'x-service-phase: OP',
            'x-thinq-app-logintype: LGE',
            'x-thinq-app-level: PRD',
            'x-thinq-app-os: IOS',
            'x-thinq-app-type: NUTS',
            'x-thinq-app-ver: 4.1.4800'
        );
    }

    public static function getPassword($_encrypted = false) {
        return $_encrypted ? hash('sha512', config::byKey('password', __CLASS__)) : config::byKey('password', __CLASS__);
    }

    public static function getUsername($_urlEncoded = false) {
        return $_urlEncoded ? urlencode(config::byKey('id', __CLASS__)) : config::byKey('id', __CLASS__);
    }

  // Étape 1
    public static function oldStep1() {
        $headers = lgthinq2::defaultHeaders();
        $headers[] = 'origin: ' . lgthinq2::LGE_MEMBERS_URL;
        $data = ['userAuth2' => lgthinq2::getPassword(true)];
        $headers[] = 'content-length: ' . strlen(http_build_query($data));
        $rep = lgthinq2::postData(lgthinq2::LGACC_SIGNIN_URL . 'signInPre', $data, $headers);
        return $rep;
    }
  
  // Étape 1
    public static function step1() {
        $headers = lgthinq2::defaultHeaders();
        $data = array(
            'user_auth2' => lgthinq2::getPassword(true),
            'log_param' => 'login request / user_id : ' . lgthinq2::getUsername() . ' / third_party : null / svc_list : SVC202,SVC710 / 3rd_service : '
        );
        $rep = lgthinq2::postData(lgthinq2::LGACC_SPX_URL . 'preLogin', $data, $headers);
        return $rep;
    }
    // Étape 2
    public static function oldStep2($rep1) {
        $headers = lgthinq2::defaultHeaders();
        $headers[] = 'sec-fetch-mode: cors';
        $headers[] = 'sec-fetch-site: same-origin';
        $headers[] = 'origin: ' . lgthinq2::LGE_MEMBERS_URL;
        $headers[] = 'referer: ' . lgthinq2::LGACC_SERVSIGNIN_URL . '?callback_url=lgaccount.lgsmartthinq:/&redirect_url=lgaccount.lgsmartthinq:/&client_id=LGAO221A02&country=FR&language=fr&state=12345&svc_code=SVC202,SVC710&close_type=0&svc_integrated=Y&webview_yn=Y&pre_login=Y';
        $data = array(
            'userId'          => lgthinq2::getUsername(true),
            'userPw'          => $rep1,
            'svcCode'         => lgthinq2::SVCCODE,
            'itgTermsUseFlag' => 'Y',
            'itgUserType'     => 'A',
            'doneYn'          => '',
            'clientId'        => lgthinq2::APPKEY,
            'local_country'   => lgthinq2::getLanguage('uppercase'),
            'local_lang'      => lgthinq2::getLanguage('lowercase')
        );
        $headers[] = 'content-length: ' . strlen(http_build_query($data));
        $rep = lgthinq2::postData(lgthinq2::LGACC_SIGNIN_URL . 'signInAct', $data, $headers);
        return $rep;
    }
    // Étape 2
    public static function step2($rep1) {
        $headers = lgthinq2::defaultHeaders();
        $headers[] = 'X-Signature: ' . $rep1['signature'];
        $headers[] = 'X-Timestamp: ' . $rep1['tStamp'];
        $data = array(
            'user_auth2' => $rep1['encrypted_pw'],
            'password_hash_prameter_flag' => 'Y',
            'svc_list' => 'SVC202,SVC710', // SVC202=LG SmartHome, SVC710=EMP OAuth
        );
        $rep = lgthinq2::postData(lgthinq2::LG_EMPTERMS_URL . 'emp/v2.0/account/session/' . lgthinq2::getUsername(true), $data, $headers);
        return $rep;
    }
    // Étape 3
    public static function step3($accountData) {
        $headers = lgthinq2::oldDefaultHeaders();
        $headers[] = 'sec-fetch-mode: cors';
        $headers[] = 'sec-fetch-site: same-origin';
        $headers[] = 'origin: ' . lgthinq2::LGE_MEMBERS_URL;
        $headers[] = 'referer: ' . lgthinq2::LGACC_SERVSIGNIN_URL . '?callback_url=lgaccount.lgsmartthinq:/&redirect_url=lgaccount.lgsmartthinq:/&client_id=LGAO221A02&country=FR&language=fr&state=12345&svc_code=SVC202&close_type=0&svc_integrated=Y&webview_yn=Y&pre_login=Y';
        $data = array(
            'loginSessionID' => $accountData['account']['loginSessionID'],
            'clientId'  => lgthinq2::APPKEY,
            'userName' => lgthinq2::getUsername(),
            'accountType' => $accountData['account']['userIDType'],
            'countryCode' => lgthinq2::getLanguage('uppercase'),
            'redirectUri' => 'lgaccount.lgsmartthinq:/',
            'state' => '12345',
            'local_country' => lgthinq2::getLanguage('uppercase'),
            'local_lang' => lgthinq2::getLanguage('lowercase'),
        );
        $rep = lgthinq2::postData(lgthinq2::LGACC_SIGNIN_URL . 'oauth', $data, $headers);
        return $rep;
    }

    // Étape 4
    public static function step4() {
        $curlTime = curl_init();
        curl_setopt_array($curlTime, array(
            CURLOPT_URL => lgthinq2::LGAPI_DATETIME,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ));
        $responseTime = curl_exec($curlTime);
        curl_close($curlTime);
        return $responseTime;
    }

    // Étape 5
    public static function step5($code, $time) {
        $headers = array(
            'content-type: application/x-www-form-urlencoded',
            'accept: application/json',
            'accept-language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
            'x-lge-appkey: ' . lgthinq2::APPKEY,
            'x-lge-app-os: IOS',
            'accept-encoding: gzip, deflate, br',
            'x-model-name: Apple/iPhone SE(2nd Gen)',
            'user-agent: LG%20ThinQ/54 CFNetwork/1410.0.3 Darwin/22.6.0',
            'x-app-version: LG ThinQ/4.1.49230',
            'x-lge-oauth-date: ' . $time['date'],
            'x-os-version: iOS/16.7',
        );

        $data4 = array(
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'lgaccount.lgsmartthinq:/',
        );
        $urlToken = "/oauth/1.0/oauth2/token?" . http_build_query($data4);
        $headers[] = 'x-lge-oauth-signature: ' . base64_encode(hash_hmac('sha1', $urlToken."\n".$time['date'], lgthinq2::OAUTHSECRETKEY, true));
        $rep = lgthinq2::postData('https://gb.lgeapi.com' . $urlToken, array(), $headers);
        return $rep;
    }

    public static function login() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('debut', __FILE__));

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : STEP 1');
        $rep1 = lgthinq2::doRetry('lgthinq2::step1');
        if (!$rep1) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 1 a échoué après plusieurs tentatives.');
            return;
        }
        $spxLogin = json_decode($rep1, true);
        if (!$spxLogin || !isset($spxLogin['encrypted_pw'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 2 a planté' . json_encode($spxLogin));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : EncryptedPw = ' . $rep1);

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : STEP 2');
        $rep2 = lgthinq2::doRetry(function() use ($spxLogin) { return lgthinq2::step2($spxLogin); }, true);
        if (!$rep2) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 2 a échoué après plusieurs tentatives.');
            return;
        }
        $accountData = json_decode($rep2, true);
        if (!$accountData || !isset($accountData['account'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 2 a planté' . json_encode($accountData));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ACCOUNT INFOS = ' . json_encode($accountData['account']));
        config::save('loginSessionID', $accountData['account']['loginSessionID'], __CLASS__);
        $timeToExp = explode(';', $accountData['account']['loginSessionID'])[1];

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : STEP 3');
        $rep3 = lgthinq2::doRetry(function() use ($accountData) { return lgthinq2::step3($accountData); });
        if (!$rep3) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 3 a échoué après plusieurs tentatives.');
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : REDIRECTURI = '. $rep3 );
        $oauth = json_decode($rep3, true);
        if (!$oauth || !isset($oauth['redirect_uri'])) {
            return;
        }
        $decodedUrl = urldecode($oauth['redirect_uri']);
        $urlParts = parse_url($decodedUrl);
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);
            if (isset($queryParams['code'])) {
                $code = $queryParams['code'];
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : URL CODE = ' .$code);
            }
            if (isset($queryParams['user_number'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : URL CODE = ' .$code);
                config::save('user_number', $queryParams['user_number'], __CLASS__);
            }
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Aucun paramètre d\'URL trouvé dans la clé redirect_uri.');
            return;
        }

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : STEP 4');
        $rep4 = lgthinq2::step4();
        if (!$rep4) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 4 a échoué.');
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : REPTIME = ' . $rep4);
        $time = json_decode($rep4, true);
        if (!$time || !isset($time['date'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Impossible de récupérer l\'heure.');
            return;
        }
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $rfc2822Date = ($time['date']?$time['date']:$dateTime->format(DateTime::RFC2822));

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : STEP 5');
        $rep5 = lgthinq2::step5($code, $time);
        if (!$rep5) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Étape 5 a échoué.');
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : FINAL TOKENS = ' . $rep5);
        $token = json_decode($rep5, true);
        if (!$token || !isset($token['access_token'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Impossible de récupérer le token d\'accès.');
            return;
        }
        config::save('access_token', $token['access_token'], __CLASS__);
        config::save('expires_in', (($timeToExp/1000) + $rep5['expires_in']), __CLASS__);
        config::save('refresh_token', $token['refresh_token'], __CLASS__);
        config::save('oauth2_backend_url', $token['oauth2_backend_url'], __CLASS__);
    }
  
    public static function getTokenIsExpired() {
        if (config::byKey('expires_in', __CLASS__, 0) < time()) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('refresh_token en cours, expiré depuis ', __FILE__) . (time() - config::byKey('expires_in', __CLASS__, 0)) . __(' secondes', __FILE__));
            return lgthinq2::refreshToken();
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('refresh_token à jour, il expire dans ', __FILE__) . (time() - config::byKey('expires_in', __CLASS__, 0)) . __(' secondes', __FILE__));
        return false;
    }

    public static function refreshToken() {
        $refreshToken = config::byKey('refresh_token', __CLASS__, '');
        if ($refreshToken != '') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('refresh_token en cours...', __FILE__));
            $headers = array(
                'x-lge-app-os: ADR',
                'x-lge-appkey: ' . lgthinq2::APPKEY,
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            );
            $data = array(
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            );
            $dateTime = new DateTime('now', new DateTimeZone('UTC'));
            $time = $dateTime->format(DateTime::RFC2822);
            $headers[] = 'x-lge-oauth-date: ' . $time;
            $urlToken = '/oauth/1.0/oauth2/token?' . http_build_query($data);
            $headers[] = 'x-lge-oauth-signature: ' . base64_encode(hash_hmac('sha1', $urlToken."\n".$time, lgthinq2::OAUTHSECRETKEY, true));
            $rep = lgthinq2::postData('https://gb.lgeapi.com' . $urlToken, array(), $headers);
            $token = json_decode($rep, true);
            if (!$token || !isset($token['access_token'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Impossible de récupérer le token d\'accès.');
                return;
            }
            config::save('access_token', $token['access_token'], __CLASS__);
            config::save('expires_in', ((time()) + $rep5['expires_in']), __CLASS__);

            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('refresh_token effectué ', __FILE__));
            return $rep;
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('Pas de refresh_token, demande de login', __FILE__));
            lgthinq2::login();
        }
    }

    public static function getDevices($_deviceId = '', $_tokenRefreshed = false) {
      
        lgthinq2::getTokenIsExpired();

        $curl = curl_init();
        $headers = lgthinq2::defaultDevicesHeaders();
        $headers[] = 'x-client-id: ' . lgthinq2::getClientId();
        $headers[] = 'x-emp-token: ' . config::byKey('access_token', __CLASS__);
        $headers[] = 'x-user-no: ' . config::byKey('user_number', __CLASS__);
        $headers[] = 'x-message-id: ' . bin2hex(random_bytes(22));
        curl_setopt_array($curl, array(
            CURLOPT_URL => lgthinq2::LGTHINQ_SERV_DEVICES . $_deviceId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __(' getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

        $response = curl_exec($curl);
        curl_close($curl);
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __(' getDEVICES : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' erreur : '. $response);
            return;
        }
        $devices = json_decode($response, true);
        if (!$devices || !isset($devices['resultCode'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Erreur de la requête  ' . json_encode($devices));
            return;
        }
        if ($devices['resultCode'] != '0000' && $_tokenRefreshed == false) {
            lgthinq2::getDevices($_deviceId, true);
        }
        $modelJson = false;
        if ($_deviceId == '') {
            foreach ($devices['result']['item'] as $items) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : $items ' . json_encode($items));
                $eqLogic = lgthinq2::createEquipement($items);
                if (is_object($eqLogic) && isset($items['modelJsonUri'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : modelJsonUri ' . $items['modelJsonUri']);
                    $modelJson = $eqLogic->getModelJson($items['modelJsonUri']);
                }
            }
        } else {
            if (isset($devices['result']['snapshot'])) {
                $eqLogic = lgthinq2::byLogicalId($devices['result']['deviceId'], __CLASS__);
                if (is_object($eqLogic)) {
                    $deviceTypeConfigFile = lgthinq2::loadConfigFile($devices['result']['deviceType']);
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : $deviceTypeConfigFile ' . json_encode($deviceTypeConfigFile));
                    if (!is_object($eqLogic->getCmd('info', 'online'))) {
                        $eqLogic->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, 'online');
                    }
                    $timestamp = null;
                    if (isset($devices['result']['snapshot']['timestamp'])) {
                        $timestamp = date('Y-m-d H:i:s', ($devices['result']['snapshot']['timestamp']/1000));
                    }
                    $eqLogic->checkAndUpdateCmd('online', $devices['result']['online'], $timestamp);
                    
                    if (isset($devices['result']['snapshot']['refState'])) {
                        //$dlConfigFile = file_get_contents(__DIR__ . '/../../data/' . $eqLogic->getLogicalId() . '.json');
                        foreach ($devices['result']['snapshot']['refState'] as $refStateId => $refStateValue) {
                            if (!is_object($eqLogic->getCmd('info', $refStateId))) {
                                $eqLogic->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $refStateId);
                            }
                            $eqLogic->checkValueAndUpdateCmd($refStateId, $refStateValue, $timestamp);
                        }
                    }
                    //$eqLogic->createCommand($deviceTypeConfigFile, $devices['result']['snapshot']);
                }
            }
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : $devices  ' . json_encode($devices));
        }
    }
  
    /**
     * Méthode appellée par le core (moteur de tâche) cron configuré dans la fonction lgthinq2_install
     * Lance une fonction pour récupérer les appareils et une fonction pour rafraichir les commandes
     * @param
     * @return
     */
    public static function update() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('début', __FILE__));
        $autorefresh = config::byKey('autorefresh', 'lgthinq2', '');
        if ($autorefresh != '') {
            try {
                $c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
                if ($c->isDue()) {
                    try {
                        lgthinq2::getTokenIsExpired();
                        foreach (eqLogic::byType('lgthinq2') as $eqLogic) {
                            if ($eqLogic->getIsEnable()) {
                                $eqLogic->refresh();
                            }
                        }
                    } catch (Exception $exc) {
                        log::add(__CLASS__, 'error', __('Erreur : ', __FILE__) . $exc->getMessage());
                    }
                }
            } catch (Exception $exc) {
                log::add(__CLASS__, 'error', __('Expression cron non valide : ', __FILE__) . $autorefresh);
            }
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('fin', __FILE__));
    }

    /**
     * Méthode appellée avant la création de l'objet
     * Active et affiche l'objet
     * @param
     * @return
     */
    public function preInsert()
    {
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    /**
     * Méthode appellée après la création de l'objet
     * Ajoute la commande refresh
     * @param
     * @return
     */
    public function postInsert()
    {
        $cmdRefresh = $this->getCmd('action', 'refresh');
        if (!is_object($cmdRefresh)) {
            $cmdRefresh = new lgthinq2Cmd();
            $cmdRefresh->setEqLogic_id($this->getId());
            $cmdRefresh->setLogicalId('refresh');
            $cmdRefresh->setName(__("Rafraîchir", __FILE__));
            $cmdRefresh->setType('action');
            $cmdRefresh->setSubType('other');
            $cmdRefresh->save();
        }
    }

    public function refresh() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('début ', __FILE__));
        lgthinq2::getDevices($this->getLogicalId());
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('fin', __FILE__));
    }

    /**
     * Recherche la commande dans le fichier de config
     * @param		string		$_key			Clé de la commande
     * @return		object		$command		Commande trouvée dans le fichier
     */
    public function checkAndCreateCmdFromConfigFile($_configData, $_key) {
        foreach ($_configData['commands'] as $command) {
            if ($command['logicalId'] == $_key) {
                $this->createCommand($command);
            }
        }
        return false;
    }
  
    public function getModelJson($_configFile) {
        if ($_configFile != '') {
            $config = file_get_contents($_configFile);
            if (!is_json($config)) {
                log::add(__CLASS__, 'debug', __('Le fichier de configuration est corrompu', __FILE__));
            }
            $data = json_decode($config, true);
            if (!is_array($data)) {
                log::add(__CLASS__, 'debug', __('Le fichier de configuration est invalide', __FILE__));
            }
            if (isset($data['MonitoringValue'])) {
                mkdir(__DIR__ . '/../../data/');
                file_put_contents(__DIR__ . '/../../data/' . $this->getLogicalId() . '.json', json_encode($data));

                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG ', __FILE__) . json_encode($data['MonitoringValue']));
                $commands = array();
                $commandsToRemove = array();
                foreach ($data['MonitoringValue'] as $key => $value) {
                    $minValue = null;
                    $maxValue = null;
                    $step = null;
                    $unit = null;
                    $targetKey = null;
                    $targetKeyValues = null;
                    
                    if ($value['dataType'] == 'enum') {
                        if (isset($value['visibleItem']['monitoringIndex']) && count($value['visibleItem']['monitoringIndex']) == 2) {
                            $subType = 'binary';
                        } else {
                            $subType = 'string';
                        }
                    } elseif($value['dataType'] == 'range') {
                        $subType = 'numeric';
                        $minValue = $value['valueMapping']['min'];
                        $maxValue = $value['valueMapping']['max'];
                        $step = $value['valueMapping']['step'];
                    } elseif($value['dataType'] == 'string') {
                        $subType = 'other';
                    } elseif($value['dataType'] == 'number') {
                        $subType = 'numeric';
                    } else {
                        $subType = 'string';
                    }
                    if (isset($value['targetKey'])) {
                        $targetKey = $value['targetKey'];
                        if (isset($value['targetKey']['tempUnit']) && count($value['targetKey']['tempUnit']) > 1) {
                            foreach ($value['targetKey']['tempUnit'] as $tempUnitId => $tempUnitValue) {
                                if (isset($data['MonitoringValue'][$tempUnitValue])) {
                                    $commandsToRemove[] = $tempUnitValue;
                                    //supprimer cette commande contenue dans targetKey
                                    $targetKeyValues[$tempUnitValue] = $data['MonitoringValue'][$tempUnitValue]['valueMapping'];
                                    log::add(__CLASS__, 'debug', __FUNCTION__ . ' TEST' . json_encode($data['MonitoringValue'][$tempUnitValue]));
                                }
                            }
                        }
                    }
                    $commands[] = array(
                        'name' => $key,
                        'logicalId' => $key,
                        'subType' => $subType,
                        'unite' => $unit,
                        'configuration' => array(
                            'minValue' => $minValue,
                            'maxValue' => $maxValue,
                            'default' => $value['default'],
                            'visibleItem' => $value['visibleItem'],
                            'valueMapping' => $value['valueMapping'],
                            'targetKey' => $targetKey,
                            'targetKeyValues' => $targetKeyValues
                        ),
                        'display' => array(
                            'parameters' => array(
                                'step' => $step
                            )
                        )
                    );
                }
                $commands = array_filter($commands, function($command) use ($commandsToRemove) {
                    return !in_array($command['logicalId'], $commandsToRemove);
                });    
                foreach ($commands as $cmd) {
                    $this->createCommand($cmd);
                }
                return $data['MonitoringValue'];
            }
        }
        return false;
    }

    public function checkValueAndUpdateCmd($refStateId, $refStateValue, $timestamp) {
        if (is_object($cmdInfo = $this->getCmd('info', $refStateId))) {
            if ($cmdInfo->getUnite() == '°C') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['CELSIUS'];
                if (isset($cmdInfo->getConfiguration('$targetKeyValues')[$tkv][$refStateValue])) {
                    return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('$targetKeyValues')[$tkv][$refStateValue]['label'], $timestamp);
                }
            } elseif ($cmdInfo->getUnite() == '°F') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['FAHRENHEIT'];
                if (isset($cmdInfo->getConfiguration('$targetKeyValues')[$tkv][$refStateValue])) {
                    return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('$targetKeyValues')[$tkv][$refStateValue]['label'], $timestamp);
                }
            }
        }
        return $this->checkAndUpdateCmd($refStateId, $refStateValue, $timestamp);
    }

    // @return		array		$data (commands.json)
    private static function loadConfigFile($_type) {
        log::add(__CLASS__, 'debug', __FUNCTION__ .' début' . $_type);
        $filename = __DIR__ . '/../../core/config/devices/' . $_type . '.json';
        if (file_exists($filename) === false) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Impossible de trouver le fichier de configuration pour l\'équipement ', __FILE__));
            return;
        }
        $content = file_get_contents($filename);
        if (!is_json($content)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration ' . $filename . ' est corrompu', __FILE__));
            return;
        }
        $data = json_decode($content, true);
        if (!is_array($data) || !isset($data['commands'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration ' . $filename . ' est invalide', __FILE__));
            return;
        }
        return $data;
    }

  
    public static function getMessagesTypeLabel($_messageType) {
        switch ($_messageType) {
            case 'communityNotificationArr':
                return 'Communauté';
                break;
            case 'deviceNotificationArr':
                return 'Appareil';
                break;
            case 'mallNotificationArr':
                return 'Centre commercial';
                break;
            case 'shareNotificationArr':
                return 'Partages';
                break;
            case 'userNotificationArr':
                return 'Utilisateur';
                break;
        }
    }

    public static function getDeviceLabel($deviceType) {
        switch ($deviceType) {
            case 'AIRFRYER':
                return 'Friteuse';
                break;
            case 'HUMIDIFIER':
                return 'Humidificateur';
                break;
            case 'LIGHT':
                return 'Lumière';
                break;
            case 'OUTLET':
                return 'Prise';
                break;
            case 'PURIFIER':
                return 'Purificateur';
                break;
            case 'SCALE':
                return 'Balance';
                break;
            case 'SWITCH':
                return 'Interrupteur';
                break;
        }
    }

    public static function getIconClass($deviceType) {
        switch ($deviceType) {
            case 'AIRFRYER':
                return 'icon nourriture-cooking14 ';
                break;
            case 'HUMIDIFIER':
                return 'icon kiko-drop';
                break;
            case 'LIGHT':
                return 'icon kiko-light-turn-on';
                break;
            case 'OUTLET':
                return 'fas fa-plug';
                break;
            case 'PURIFIER':
                return 'fas fa-wind';
                break;
            case 'SCALE':
                return 'fas fa-weight';
                break;
            case 'SWITCH':
                return 'icon kiko-off';
                break;
        }
    }

    /**
     * Créé l'équipement avec les valeurs de paramètres
     * @param		array		$_data		Tableau des paramètres
     * @return		object		$eqLogic	Retourne l'équipement créé
     */
    public static function createEquipement($_capa) {
        log::add(__CLASS__, 'debug', __FUNCTION__ .' début' . json_encode($_capa));
        if (!isset($_capa['deviceId'])) {
            log::add(__CLASS__, 'error', __FUNCTION__ .' erreur uuid inexistant ' . json_encode($_capa));
            return;
        }
        $eqLogic = lgthinq2::byLogicalId($_capa['deviceId'], __CLASS__);
        if (!is_object($eqLogic)) {
            $eqLogic = new lgthinq2();
            $eqLogic->setName($_capa['alias']);
            $eqLogic->setLogicalId($_capa['deviceId']);
            $eqLogic->setObject_id(null);
            $eqLogic->setEqType_name(__CLASS__);
            $eqLogic->setIsEnable(1);
            $eqLogic->setIsVisible(1);

            if (isset($_capa['deviceType'])) {
                $eqLogic->setConfiguration('deviceType', $_capa['deviceType']);
            }
            if (isset($_capa['homeId'])) {
                $eqLogic->setConfiguration('homeId', $_capa['homeId']);
            }
            if (isset($_capa['deviceId'])) {
                $eqLogic->setConfiguration('deviceId', $_capa['deviceId']);
            }
            if (isset($_capa['roomId'])) {
                $eqLogic->setConfiguration('roomId', $_capa['roomId']);
            }
            if (isset($_capa['modelName'])) {
                $eqLogic->setConfiguration('modelName', $_capa['modelName']);
            }
            $eqLogic->save();
            event::add('jeedom::alert', array(
                          'level' => 'success',
                          'page' => __CLASS__,
                          'message' => __('L\'équipement ', __FILE__) . $eqLogic->getHumanName() . __(' vient d\'être créé', __FILE__),
            ));
        }
        return $eqLogic;
    }

    // @return		bool	(true)
	public function isConnected()
    {
		$cmdConnected = $this->getCmd('info', 'online');
		if (is_object($cmdConnected)) {
			if ($this->getIsEnable() && $cmdConnected->execCmd()) {
				return true;
			} else {
				return false;
			}
		} else {
			log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Commande online inexistante : ', __FILE__) . $this->getConfiguration('deviceType', '') . ' ' . $this->getLogicalId());
		}
	}

    public static function synchronize()
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('début', __FILE__));
        lgthinq2::getDevices();
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('fin', __FILE__));
    }

    public function createCommand($_properties, $_cmdInfo = null)
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('début ', __FILE__) . $this->getName() . ' ' . json_encode($_properties));
        $cmd = $this->getCmd(null, $_properties['logicalId']);
        if (!is_object($cmd)) {
            $cmd = new lgthinq2Cmd();
            $cmd->setType((!$_cmdInfo?'info':'action'));
            $cmd->setEqLogic_id($this->getId());
            utils::a2o($cmd, $_properties);
            $cmd->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' => ' . __('Nouvelle commande ajoutée ', __FILE__) . '[' . $cmd->getType() .'='. $cmd->getSubType() . '] => ' . $cmd->getLogicalId());
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ' . __('fin', __FILE__));
        return $cmd;
    }

    /**
     * Renvoi le lien de l'image de l'object eqLogic
     * @return		string		url		url du fichier image
     */
    public function getImage()
    {
        return 'plugins/lgthinq2/core/config/img/' . $this->getConfiguration('thumbnail', 'none');
    }

    /**
     * Créé l'équipement avec les valeurs de paramètres
     * @param		string		$_version	Dashboard ou mobile
     * @return		string		$html		Retourne la page générée de l'équipement
     */
    public function toHtml($_version = 'dashboard')
    {
        if ($this->getConfiguration('widgetTemplate') != 1) {
            return parent::toHtml($_version);
        }
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
		$_version = jeedom::versionAlias($_version);

        foreach ($this->getCmd('info', null) as $cmd) {
            $replace['#cmd_' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            $replace['#cmd_' . $cmd->getLogicalId() . '_name#'] = $cmd->getName();
            $replace['#cmd_' . $cmd->getLogicalId() . '_value#'] = $cmd->execCmd();
            $replace['#cmd_' . $cmd->getLogicalId() . '_icon#'] = $cmd->getDisplay('icon', '');
            if ($cmd->getConfiguration('maxValue', '') != '') {
                $replace['#cmd_' . $cmd->getLogicalId() . '_maxValue#'] = $cmd->getConfiguration('maxValue');
            }
            $replace['#cmd_' . $cmd->getLogicalId() . '_unite#'] = $cmd->getUnite();
            $replace['#cmd_' . $cmd->getLogicalId() . '_collectDate#'] = $cmd->getCollectDate();
            $replace['#cmd_' . $cmd->getLogicalId() . '_valueDate#'] = $cmd->getValueDate();
        }
        foreach ($this->getCmd('action', null) as $cmdAction) {
            $parts = explode('::', $cmdAction->getLogicalId());
            $replace['#cmdAction_' . $parts[1] . '_id#'] = $cmdAction->getId();
            $replace['#cmdAction_' . $parts[1] . '_name#'] = $cmdAction->getName();
            if ($cmdAction->getConfiguration('maxValue', '') != '') {
                $replace['#cmd_' . $cmd->getLogicalId() . '_maxValue#'] = $cmdAction->getConfiguration('maxValue');
            }
            $replace['#cmdAction_' . $parts[1] . '_unite#'] = $cmdAction->getUnite();
            $replace['#cmdAction_' . $parts[1] . '_collectDate#'] = $cmdAction->getCollectDate();
            $replace['#cmdAction_' . $parts[1] . '_valueDate#'] = $cmdAction->getValueDate();
        }

		$html = template_replace($replace, getTemplate('core', $_version, 'lgthinq2.template',__CLASS__));
        $html = translate::exec($html, 'plugins/lgthinq2/core/template/' . $version . '/lgthinq2.tempate.html');
        return $html;
    }
}

class lgthinq2Cmd extends cmd
{
    public static $_widgetPossibility = array('custom' => true);

    public function execute($_options = array())
    {
        $eqLogic = $this->getEqLogic();
        log::add('lgthinq2', 'debug', __("Action sur ", __FILE__) . $this->getLogicalId() . __(" avec options ", __FILE__) . json_encode($_options));
        $parts = explode('::', $this->getLogicalId());

        if ($this->getLogicalId() == 'refresh') {
            $eqLogic->refresh();
            return;
        }
        if (count($parts) !== 2) {
            log::add('lgthinq2', 'debug', __("Nombre d'arguments incorrect dans la commande ", __FILE__) . $this->getLogicalId());
            return;
        }

        switch ($this->getSubType()) {
            case 'slider':
                $replace['#slider#'] = floatval($_options['slider']);
                break;
            case 'color':
                $replace['#color#'] = $_options['color'];
                break;
            case 'select':
                $replace['#select#'] = $_options['select'];
                break;
            case 'message':
                $replace['#title#'] = $_options['title'];
                $replace['#message#'] = $_options['message'];
                if ($_options['message'] == '' && $_options['title'] == '') {
                  throw new Exception(__('Le message et le sujet ne peuvent pas être vide', __FILE__));
                }
                break;
        }
        $value = str_replace(array_keys($replace),$replace,$this->getConfiguration('updateCmdToValue', ''));
        log::add('lgthinq2', 'debug', __("Données à envoyer : ", __FILE__) . $parts[0] . '=' . $parts[1] . '=>' . $value);

    }
}