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
require_once __DIR__ . '/../../../../plugins/lgthinq2/core/class/lgthinq2.customLang.php';

class lgthinq2 extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '0.50';

    const LGTHINQ_GATEWAY       = 'https://route.lgthinq.com:46030/v1/service/application/gateway-uri';
    const LGTHINQ_GATEWAY_LIST  = 'https://kic.lgthinq.com:46030/api/common/gatewayUriList';
    const LGAPI_DATETIME        = 'https://fr.lgeapi.com/datetime';
    const LGTHINQ1_SERV_DEVICES = 'https://eic.lgthinq.com:46030/api/';
    const LGTHINQ2_SERV_DEVICES = 'https://eic-service.lgthinq.com:46030/v1/service/devices/';

    const APPLICATION_KEY       = '6V1V8H2BN5P9ZQGOI5DAQ92YZBDO3EK9';
    const OAUTHSECRETKEY        = 'c053c2a6ddeb7ad97cb0eed0dcb31cf8';
    const APPKEY                = 'LGAO221A02';
    const SVCCODE               = 'SVC202';
    const XAPIKEY               = 'VGhpblEyLjAgU0VSVklDRQ=='; //base64 "ThinQ2.0 SERVICE"
    const DATA_ROOT             = 'lgedmRoot';
    const MAXRETRY              = 3;

    /**
     * Renvoie le libellé correspondant à un type de périphérique à partir de son identifiant.
     *
     * Cette méthode prend en paramètre l'identifiant numérique d'un type de périphérique et renvoie son libellé associé.
     * Si aucun libellé correspondant n'est trouvé pour l'identifiant donné, l'identifiant lui-même est renvoyé.
     *
     * @param int $_id L'identifiant numérique du type de périphérique.
     * @return string Le libellé correspondant au type de périphérique ou l'identifiant lui-même s'il n'existe pas de libellé correspondant.
     */
    public static function deviceTypeConstants($_id) {
        $_deviceTypes = array(
            000 => __('Inconnu', __FILE__),
            101 => __('Réfrigérateur', __FILE__),
            102 => __('Réfrigérateur à kimchi', __FILE__),
            103 => __('Purificateur d\'eau', __FILE__),
            105 => __('Cave à vin', __FILE__),
            201 => __('Lave-linge', __FILE__),
            202 => __('Sèche-linge', __FILE__),
            203 => __('Styler', __FILE__),
            204 => __('Lave-vaisselle', __FILE__),
            221 => __('WashTower laveuse', __FILE__),
            222 => __('WashTower sécheuse', __FILE__),
            301 => __('Four', __FILE__),
            302 => __('Four micro-ondes', __FILE__),
            303 => __('Table de cuisson', __FILE__),
            304 => __('Hotte', __FILE__),
            401 => __('Climatisation/Air Care/Pompe à chaleur', __FILE__),
            402 => __('Purificateur d\'air', __FILE__),
            403 => __('Déshumidificateur', __FILE__),
            405 => __('Ventilateur de plafond', __FILE__),
            501 => __('Aspirateur robot', __FILE__),
            504 => __('Aspirateur balai', __FILE__),
            604 => __('Chaise de massage', __FILE__),
            605 => __('Thermostat de luxe', __FILE__),
            1001 => __('Arch', __FILE__),
            3001 => __('Missg', __FILE__),
            3002 => __('Capteur ThinQ', __FILE__),
            3003 => __('Ampoule LG', __FILE__),
            3004 => __('Détecteur de mouvement', __FILE__),
            3005 => __('Prise DW', __FILE__),
            3006 => __('Capteur de poussière', __FILE__),
            3010 => __('Détecteur de fumée', __FILE__),
            3014 => __('Prise Easy', __FILE__),
            3102 => __('Détecteur solaire', __FILE__),
            3103 => __('Type de groupe d\'éclairage', __FILE__),
            3007 => __('Capteur de gaz Orbivo', __FILE__),
            3008 => __('Détecteur de fuite d\'eau Orbivo', __FILE__),
            3009 => __('Détecteur de mouvement Ihorn', __FILE__),
            3011 => __('Capteur de monoxyde de carbone Orbivo', __FILE__),
            3012 => __('Sonde de température Orbivo', __FILE__),
            3015 => __('Capteur d\'humidité Orbivo', __FILE__),
            3013 => __('Détecteur d\'ouverture de porte Ihorn', __FILE__),
            4001 => __('EMS_AIR_STATION', __FILE__),
            4003 => __('Sonde air', __FILE__),
            4004 => __('Capteur de poussière', __FILE__),
            4006 => __('Lampe intelligente', __FILE__),
            4201 => __('Détecteur de mouvement Aqara', __FILE__),
            4202 => __('Capteur thermométrique/hygrométrique Aqara', __FILE__),
            4203 => __('Capteur d\'ouverture Aqara', __FILE__),
            4301 => __('Prise Aqara', __FILE__),
            10000 => __('Télévision', __FILE__),
            10101 => __('Hub HEJ', __FILE__),
            20000 => __('Montre', __FILE__)
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:$_id;
    }

    /**
     * Renvoie le libellé correspondant à un code de type de périphérique à partir de son identifiant.
     *
     * Cette méthode prend en paramètre l'identifiant d'un code de type de périphérique et renvoie son libellé associé.
     * Si aucun libellé correspondant n'est trouvé pour l'identifiant donné, la méthode renvoie false.
     *
     * @param string $_id L'identifiant du code de type de périphérique.
     * @return string|false Le libellé correspondant au code de type de périphérique, ou false si aucun libellé n'est trouvé.
     */
    public static function deviceTypeCodeConstants($_id) {
        $_deviceTypes = array(
            'AI01'    => __('Climatisation/Air Care', __FILE__),
            'AI04'    => __('Climatiseur commercial', __FILE__),
            'AI05'    => __('Pompe à chaleur air-eau', __FILE__),
            'AI07'    => __('Chauffe-eau', __FILE__),
            'AI08'    => __('Ventilation', __FILE__),
            'AI09'    => __('Cloud Gateway', __FILE__),
            'DUCT'    => __('Gainable', __FILE__),
            'ETC'     => __('Autre modèle', __FILE__),
            'GRAM'    => __('gram', __FILE__),
            'KI0101'  => __('Réfrigérateur side by side', __FILE__),
            'KI0102'  => __('Réfrigérateur deux portes', __FILE__),
            'KI0103'  => __('Réfrigérateur congélateur en haut', __FILE__),
            'KI0104'  => __('Réfrigérateur congélateur en bas', __FILE__),
            'KI03'    => __('Four', __FILE__),
            'KI06'    => __('Lave-vaisselle', __FILE__),
            'KI07'    => __('Four micro-ondes', __FILE__),
            'KI08'    => __('Table de cuisson', __FILE__),
            'KI09'    => __('Hotte', __FILE__),
            'KI10'    => __('Cave à vin', __FILE__),
            'LA01'    => __('Lave-linge top', __FILE__),
            'LA02'    => __('Lave-linge hublot', __FILE__),
            'LA03'    => __('Sèche-linge', __FILE__),
            'LA04'    => __('Styler', __FILE__),
            'LA05'    => __('Lave-linge sur piédestal', __FILE__),
            'LA06'    => __('WashTower', __FILE__),
            'LI01'    => __('Aspirateur robot', __FILE__),
            'LI02'    => __('Purificateur d\'air', __FILE__),
            'LI04'    => __('Aspirateur balai', __FILE__),
            'MAT5000' => __('Éclairage', __FILE__),
            'MAT5001' => __('Éclairage', __FILE__),
            'MAT5002' => __('Éclairage', __FILE__),
            'MAT5003' => __('Éclairage', __FILE__),
            'MAT5100' => __('Raccordement', __FILE__),
            'MAT5101' => __('Raccordement', __FILE__),
            'MAT5102' => __('Pompe', __FILE__),
            'MAT5205' => __('Interrupteur', __FILE__),
            'MAT5300' => __('Capteur de contact', __FILE__),
            'MAT5301' => __('Capteur de lumière', __FILE__),
            'MAT5302' => __('Capteur d’occupation', __FILE__),
            'MAT5303' => __('Capteur de température', __FILE__),
            'MAT5304' => __('Capteur de pression', __FILE__),
            'MAT5305' => __('Capteur de débit', __FILE__),
            'MAT5307' => __('Capteur d’humidité', __FILE__),
            'MAT5400' => __('Verrouillage de porte', __FILE__),
            'MAT5402' => __('Couverture de fenêtre', __FILE__),
            'MAT5500' => __('Unité de chauffage / refroidissement"', __FILE__),
            'MAT5501' => __('Thermostat', __FILE__),
            'MAT5502' => __('Capteur de lumière', __FILE__),
            'POT'     => __('Climatiseur mobile', __FILE__),
            'RAC'     => __('Climatiseur mural', __FILE__),
            'SH'      => __('Hub IoT', __FILE__),
            'SPAC'    => __('Appareil sur pieds', __FILE__),
            'SRAC'    => __('Climatiseur mural', __FILE__),
            'ULTRAPC' => __('UltraPC', __FILE__)
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:false;
    }

    /* Renvoie le libellé correspondant à un code de type de périphérique à partir de son identifiant.
     *
     * Cette méthode prend en paramètre l'identifiant d'un code de type de périphérique et renvoie son libellé associé.
     * Si aucun libellé correspondant n'est trouvé pour l'identifiant donné, la méthode renvoie false.
     *
     * @param string $_id L'identifiant du code de type de périphérique.
     * @return string|false Le libellé correspondant au code de type de périphérique, ou false si aucun libellé n'est trouvé.
     */
    public static function deviceTypeConstantsIcon($_id) {
        $_deviceTypes = array(
            000 => '', // Inconnu
            101 => 'fa fa-snowflake', // Réfrigérateur
            102 => 'fa fa-ice-cream', // Réfrigérateur à kimchi
            103 => 'fa fa-tint', // Purificateur d'eau
            105 => 'fa fa-wine-glass', // Cave à vin
            201 => 'fa fa-tshirt', // Lave-linge
            202 => 'fa fa-tshirt', // Sèche-linge
            203 => 'fa fa-tshirt', // Styler
            204 => 'fa fa-utensils', // Lave-vaisselle
            221 => 'fa fa-tshirt', // WashTower laveuse
            222 => 'fa fa-tshirt', // WashTower sécheuse
            301 => 'fa fa-utensils', // Four
            302 => 'fa fa-microwave', // Four micro-ondes
            303 => 'fa fa-burner', // Table de cuisson
            304 => 'fa fa-exhaust-hood', // Hotte
            401 => 'fa fa-snowflake', // Climatisation/Air Care/Pompe à chaleur
            402 => 'fa fa-wind', // Purificateur d'air
            403 => 'fa fa-tint', // Déshumidificateur
            405 => 'fa fa-fan', // Ventilateur de plafond
            501 => 'fa fa-robot', // Aspirateur robot
            504 => 'fa fa-broom', // Aspirateur balai
            604 => 'fa fa-chair', // Chaise de massage
            605 => 'fa fa-thermometer', // Thermostat de luxe
            1001 => 'fa fa-couch', // Arch
            3001 => 'fa fa-box', // Missg
            3002 => 'fa fa-microchip', // Capteur ThinQ
            3003 => 'fa fa-lightbulb', // Ampoule LG
            3004 => 'fa fa-walking', // Détecteur de mouvement
            3005 => 'fa fa-plug', // Prise DW
            3006 => 'fa fa-dust', // Capteur de poussière
            3010 => 'fa fa-fire', // Détecteur de fumée
            3014 => 'fa fa-plug', // Prise Easy
            3102 => 'fa fa-sun', // Détecteur solaire
            3103 => 'fa fa-lightbulb', // Type de groupe d'éclairage
            3007 => 'fa fa-gas-pump', // Capteur de gaz Orbivo
            3008 => 'fa fa-water', // Détecteur de fuite d'eau Orbivo
            3009 => 'fa fa-walking', // Détecteur de mouvement Ihorn
            3011 => 'fa fa-skull-crossbones', // Capteur de monoxyde de carbone Orbivo
            3012 => 'fa fa-thermometer', // Sonde de température Orbivo
            3015 => 'fa fa-tint', // Capteur d'humidité Orbivo
            3013 => 'fa fa-door-open', // Détecteur d'ouverture de porte Ihorn
            4001 => 'fa fa-wind', // EMS_AIR_STATION
            4003 => 'fa fa-wind', // Sonde air
            4004 => 'fa fa-dust', // Capteur de poussière
            4006 => 'fa fa-lightbulb', // Lampe intelligente
            4201 => 'fa fa-walking', // Détecteur de mouvement Aqara
            4202 => 'fa fa-thermometer', // Capteur thermométrique/hygrométrique Aqara
            4203 => 'fa fa-door-open', // Capteur d'ouverture Aqara
            4301 => 'fa fa-plug', // Prise Aqara
            10000 => 'fa fa-tv', // Télévision
            10101 => 'fa fa-hdd', // Hub HEJ
            20000 => 'fa fa-clock' // Montre
        );

        return isset($_deviceTypes[$_id]) ? $_deviceTypes[$_id] : '';
    }

    /**
     * Renvoie l'état correspondant à un type de périphérique à partir de son identifiant.
     *
     * Cette méthode prend en paramètre l'identifiant numérique d'un type de périphérique et renvoie l'état associé.
     * Si aucun état correspondant n'est trouvé pour l'identifiant donné, la méthode renvoie false.
     *
     * @param int|string $_id L'identifiant numérique ou alphabétique du type de périphérique.
     * @return string|false L'état correspondant au type de périphérique, ou false si aucun état n'est trouvé.
     */
    public static function deviceTypeConstantsState($_id) {
        $_deviceTypes = array(
            101 => 'refState',
            102 => 'kmcState',
            103 => 'wpState',
            104 => 'btState',
            105 => 'wnState',
            106 => 'hvState',
            201 => 'washerDryer',
            202 => 'washerDryer',
            203 => 'styler',
            204 => 'dishwasher',
            205 => 'shoeStyler',
            221 => 'washerDryer',
            222 => 'washerDryer',
            'HAB' => 'mSheet.OVEN_TYPE, "ovenState',
            302 => 'ovenState,otrState',
            303 => 'cooktopState',
            304 => 'hoodState',
            305 => 'smallthingState',
            306 => 'smallthingState',
            501 => 'robotkingState',
            504 => 'qmState',
            604 => 'massageChair',
            701 => 'ess'
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:false;
    }

    /**
     * Vérifie si une chaîne de caractères est un JSON valide.
     *
     * Cette fonction vérifie si une chaîne de caractères est un JSON valide.
     *
     * @param string $string La chaîne de caractères à vérifier.
     * @return bool True si la chaîne est un JSON valide ; sinon, false.
     */
    public static function isValidJson($string) {
        if ($string !== false && $string !== null && $string !== '') {
            json_decode($string);
            if (json_last_error() === JSON_ERROR_NONE) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remplace le début d'une chaîne donnée.
     *
     * Cette fonction remplace la chaîne de début $_needle par une chaîne vide dans $_haystack si $_haystack commence par $_needle.
     *
     * @param string $_needle La chaîne à rechercher au début de $_haystack.
     * @param string $_haystack La chaîne à modifier.
     * @return string|false La chaîne modifiée si le remplacement a réussi ; sinon, retourne false.
     */
    public static function replaceBeginString($_needle, $_haystack) {
        if (substr($_haystack, 0, strlen($_needle)) == $_needle) {
            return substr($_haystack, strlen($_needle));
        }
        return false;
    }

    /**
     * Effectue une requête POST vers une URL donnée avec les données et les en-têtes spécifiés.
     *
     * Cette méthode prend en paramètres l'URL cible, les données à envoyer et les en-têtes HTTP à inclure dans la requête.
     * Elle effectue une requête POST vers l'URL avec les paramètres spécifiés et renvoie la réponse obtenue.
     *
     * @param string $url L'URL cible de la requête POST.
     * @param array|string $data Les données à envoyer dans la requête POST, sous forme de tableau associatif ou de chaîne de requête.
     * @param array $headers Les en-têtes HTTP à inclure dans la requête POST, sous forme de tableau.
     * @return string|false La réponse de la requête POST, ou false en cas d'échec.
     */
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
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * Effectue plusieurs tentatives pour exécuter une fonction donnée.
     *
     * Cette méthode prend en paramètres la fonction à exécuter et un indicateur indiquant si le contenu brut doit être renvoyé en cas de succès.
     * Elle exécute la fonction donnée plusieurs fois (jusqu'à un maximum de tentatives) et renvoie le résultat de la dernière tentative réussie.
     * Si la fonction échoue à chaque tentative, la méthode renvoie null.
     *
     * @param callable $stepFunction La fonction à exécuter.
     * @param bool $rawContent Indique si le contenu brut doit être renvoyé en cas de succès.
     * @return mixed|null Le résultat de la dernière tentative réussie, ou null en cas d'échec.
     */
    public static function doRetry($stepFunction, $rawContent = false) {
        $result = null;
        for ($i = 1; $i <= lgthinq2::MAXRETRY; $i++) {
            $result = $stepFunction();
            if (!$result) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape a échoué, tentative ', __FILE__) . $i . '/' . lgthinq2::MAXRETRY);
            } else {
                if ($rawContent) {
                    $res = json_decode($result, true);
                    if ($res && isset($res['error']) && isset($res['error']['message'])) {
                        log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape a échoué ', __FILE__) . $res['error']['message'] . ', tentative ' . $i . '/' . lgthinq2::MAXRETRY);
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

    /**
     * Renvoie l'identifiant du client, en le générant s'il n'existe pas déjà.
     *
     * Cette méthode renvoie l'identifiant du client stocké dans la configuration de la classe.
     * Si aucun identifiant de client n'est trouvé, un nouvel identifiant est généré et stocké dans la configuration.
     *
     * @return string L'identifiant du client.
     */
    public static function getClientId() {
         if (config::byKey('cliend_id', __CLASS__, '') == '') {
             log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Création du client_id ', __FILE__));
             config::save('cliend_id', bin2hex(random_bytes(32)), __CLASS__);
         }
         return config::byKey('cliend_id', __CLASS__);
    }

    /**
     * Renvoie la langue configurée sous différents formats.
     *
     * Cette méthode renvoie la langue configurée dans la classe, sous différents formats en fonction du type spécifié.
     * Les types supportés sont : 'lowercase', 'uppercase', 'hyphen' et 'plain'.
     *
     * @param string $_type Le type de format de langue à renvoyer.
     * @return string La langue configurée sous le format spécifié.
     */
    public static function getLanguage($_type) {
        $lang = config::byKey('language', __CLASS__, 'fr_FR');
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

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les anciens services LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les anciens services LG ThinQ.
     */
    public static function oldDefaultHeaders() {
        return array(
            'Accept: */*',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'origin: ' . config::byKey('LGE_MEMBERS_URL', __CLASS__),
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            'X-Requested-With: XMLHttpRequest'
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les services LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les services LG ThinQ.
     */
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
            'X-Device-Language: ' . str_replace('_', '-', config::byKey('language', __CLASS__, 'fr_FR')),
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Access-Control-Allow-Origin: *',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen')  . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les passerelles LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les passerelles LG ThinQ.
     */
    public static function defaultGwHeaders() {
        return array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ';q=1',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: LG ThinQ/4.1.49230 (iPhone; iOS 16.7; Scale/2.00)',
            'x-api-key: ' . lgthinq2::XAPIKEY,
            'x-app-version: 4.1.49230',
            'x-client-id: ' . lgthinq2::getClientId(),
            'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
            'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
            'x-message-id: ' . bin2hex(random_bytes(22)),
            'x-model-name: iPhone SE(2nd Gen)',
            'x-origin: app-native',
            'x-os-version: 16.7',
            'x-service-code: ' . lgthinq2::SVCCODE,
            'x-service-phase: OP',
            'x-thinq-app-logintype: LGE',
            'x-thinq-app-level: PRD',
            'x-thinq-app-os: IOS',
            'x-thinq-app-type: NUTS',
            'x-thinq-app-ver: 4.1.4800',
            'x-user-no: ' . config::byKey('user_number', __CLASS__)
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ.
     */
    public static function defaultDevicesHeaders() {
        return array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ';q=1',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: LG ThinQ/4.1.49230 (iPhone; iOS 16.7; Scale/2.00)',
            'x-api-key: ' . lgthinq2::XAPIKEY,
            'x-app-version: 4.1.49230',
            'x-client-id: ' . lgthinq2::getClientId(),
            'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
            'x-emp-token: ' . config::byKey('access_token', __CLASS__),
            'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
            'x-message-id: ' . bin2hex(random_bytes(22)),
            'x-model-name: iPhone SE(2nd Gen)',
            'x-origin: app-native',
            'x-os-version: 16.7',
            'x-service-code: ' . lgthinq2::SVCCODE,
            'x-service-phase: OP',
            'x-thinq-app-logintype: LGE',
            'x-thinq-app-level: PRD',
            'x-thinq-app-os: IOS',
            'x-thinq-app-type: NUTS',
            'x-thinq-app-ver: 4.1.4800',
            'x-user-no: ' . config::byKey('user_number', __CLASS__)
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ avec EMP.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ avec EMP.
     */
    public static function defaultDevicesEmpHeaders() {
        return array(
            'Accept: application/json',
            'Content-Type: application/json',
            'x-thinq-application-key: wideq',
            'x-thinq-security-key: nuts_securitykey',
            'x-thinq-token: ' . config::byKey('access_token', __CLASS__)
        );
    }

    /**
     * Renvoie le mot de passe configuré, éventuellement hashé en SHA-512.
     *
     * @param bool $_encrypted Indique si le mot de passe doit être hashé en SHA-512.
     * @return string Le mot de passe configuré.
     */
    public static function getPassword($_encrypted = false) {
        return $_encrypted ? hash('sha512', config::byKey('password', __CLASS__)) : config::byKey('password', __CLASS__);
    }

    /**
     * Renvoie le nom d'utilisateur configuré, éventuellement encodé pour une utilisation dans une URL.
     *
     * @param bool $_urlEncoded Indique si le nom d'utilisateur doit être encodé pour une utilisation dans une URL.
     * @return string Le nom d'utilisateur configuré.
     */
    public static function getUsername($_urlEncoded = false) {
        return $_urlEncoded ? urlencode(config::byKey('id', __CLASS__)) : config::byKey('id', __CLASS__);
    }

    /**
     * Étape 0 : Obtient les informations de la passerelle LG ThinQ.
     *
     * @return bool|string Retourne true en cas de succès, sinon retourne null.
     */
    public static function step0() {
        $headers = lgthinq2::defaultGwHeaders();
        $curlGw = curl_init();
        curl_setopt_array($curlGw, array(
            CURLOPT_URL => lgthinq2::LGTHINQ_GATEWAY,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        $rep = curl_exec($curlGw);
        curl_close($curlGw);
        $gatewayRes = json_decode($rep, true);
        if (!$gatewayRes || !isset($gatewayRes['result'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 0 a planté ', __FILE__) . json_encode($gatewayRes));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 0 a result ', __FILE__) . parse_url($gatewayRes['result']['empFrontBaseUri2'], PHP_URL_HOST));

        config::save('LGE_MEMBERS_URL', 'https://' . parse_url($gatewayRes['result']['uris']['empFrontBaseUri2'], PHP_URL_HOST), __CLASS__);
        config::save('LG_EMPTERMS_URL', $gatewayRes['result']['empTermsUri'], __CLASS__);
        config::save('LGACC_SPX_URL', $gatewayRes['result']['empSpxUri'], __CLASS__);

        return true;
    }

    /**
     * Étape 1 : Effectue la première étape de connexion.
     *
     * @return string|bool Retourne la réponse de la requête POST si réussie, sinon retourne false.
     */
    public static function step1() {
        $headers = lgthinq2::defaultHeaders();
        $data = array(
            'user_auth2' => lgthinq2::getPassword(true),
            'log_param' => 'login request / user_id : ' . lgthinq2::getUsername() . ' / third_party : null / svc_list : SVC202,SVC710 / 3rd_service : '
        );
        $rep = lgthinq2::postData(config::byKey('LGACC_SPX_URL', __CLASS__) . '/preLogin', http_build_query($data), $headers);
        return $rep;
    }

    /**
     * Ancienne Étape 2 : Effectue la deuxième étape de connexion.
     *
     * @param string $rep1 La réponse de l'étape précédente.
     * @return string|bool Retourne la réponse de la requête POST si réussie, sinon retourne false.
     */
    public static function oldStep2($rep1) {
        $headers = lgthinq2::defaultHeaders();
        $headers[] = 'sec-fetch-mode: cors';
        $headers[] = 'sec-fetch-site: same-origin';
        $headers[] = 'origin: ' . config::byKey('LGE_MEMBERS_URL', __CLASS__);
        $headers[] = 'referer: ' . config::byKey('LGE_MEMBERS_URL', __CLASS__) . '/lgacc/service/v1/signin?callback_url=lgaccount.lgsmartthinq:/&redirect_url=lgaccount.lgsmartthinq:/&client_id=LGAO221A02&country=FR&language=fr&state=12345&svc_code=SVC202,SVC710&close_type=0&svc_integrated=Y&webview_yn=Y&pre_login=Y';
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
        $rep = lgthinq2::postData(config::byKey('LGE_MEMBERS_URL', __CLASS__) . '/lgacc/front/v1/signin/signInAct', http_build_query($data), $headers);
        return $rep;
    }

    /**
     * Étape 2 : Effectue la deuxième étape de connexion.
     *
     * @param array $rep1 La réponse de l'étape précédente.
     * @return string|bool Retourne la réponse de la requête POST si réussie, sinon retourne false.
     */
    public static function step2($rep1) {
        $headers = lgthinq2::defaultHeaders();
        $headers[] = 'X-Signature: ' . $rep1['signature'];
        $headers[] = 'X-Timestamp: ' . $rep1['tStamp'];
        $data = array(
            'user_auth2' => $rep1['encrypted_pw'],
            'password_hash_prameter_flag' => 'Y',
            'svc_list' => 'SVC202,SVC710', // SVC202=LG SmartHome, SVC710=EMP OAuth
        );
        $rep = lgthinq2::postData(config::byKey('LG_EMPTERMS_URL', __CLASS__) . '/emp/v2.0/account/session/' . lgthinq2::getUsername(true), http_build_query($data), $headers);
        return $rep;
    }

    /**
     * Étape 3 : Effectue la troisième étape de connexion.
     *
     * @param array $accountData Les données du compte.
     * @return string|bool Retourne la réponse de la requête POST si réussie, sinon retourne false.
     */
    public static function step3($accountData) {
        $headers = lgthinq2::oldDefaultHeaders();
        $headers[] = 'referer: ' . config::byKey('LGE_MEMBERS_URL', __CLASS__) . '/lgacc/service/v1/signin?callback_url=lgaccount.lgsmartthinq:/&redirect_url=lgaccount.lgsmartthinq:/&client_id=LGAO221A02&country=FR&language=fr&state=12345&svc_code=SVC202&close_type=0&svc_integrated=Y&webview_yn=Y&pre_login=Y';
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
        $rep = lgthinq2::postData(config::byKey('LGE_MEMBERS_URL', __CLASS__) . '/lgacc/front/v1/signin/oauth', http_build_query($data), $headers);
        return $rep;
    }

    /**
     * Étape 4 : Obtient la date et l'heure à partir de l'API LG.
     *
     * @return string|bool Retourne la réponse de la requête GET si réussie, sinon retourne false.
     */
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

    /**
     * Étape 5 : Effectue la cinquième étape de connexion.
     *
     * @param string $code Le code d'autorisation.
     * @param array $time Les informations de date et d'heure.
     * @return string|bool Retourne la réponse de la requête POST si réussie, sinon retourne false.
     */
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
        $rep = lgthinq2::postData('https://gb.lgeapi.com' . $urlToken, '', $headers);
        return $rep;
    }

    /**
     * Étape 6 : Ancienne méthode de connexion à thinq1.
     *
     * @return string|bool Retourne l'ID de session si réussi, sinon retourne null.
     */
    public static function step6() {
        $headers = lgthinq2::defaultDevicesEmpHeaders();

        $data = array(
            lgthinq2::DATA_ROOT => array(
                'countryCode' => lgthinq2::getLanguage('uppercase'),
                'langCode' => lgthinq2::getLanguage('hyphen'),
                'loginType' => 'EMP',
                'token' => config::byKey('access_token', __CLASS__)
            )
        );

        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'member/login', json_encode($data, JSON_PRETTY_PRINT), $headers);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 6 a échoué.', __FILE__));
            return;
        }
        $arr6 = json_decode($response, true);
        if (!$arr6 || !isset($arr6[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Erreur de la requête ', __FILE__) . json_encode($arr6));
            return;
        }
        if (!isset($arr6[lgthinq2::DATA_ROOT]['returnCd'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Erreur de la réponse ', __FILE__) . json_encode($arr6[lgthinq2::DATA_ROOT]));
            return;
        }
        if ($arr6[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Code retour erroné ', __FILE__) . json_encode($arr6[lgthinq2::DATA_ROOT]));
            return;
        }
        return $arr6[lgthinq2::DATA_ROOT]['jsessionId'];
    }

    /**
     * Méthode de connexion.
     */
    public static function login() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . __('debut', __FILE__));

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' : ÉTAPE 0', __FILE__));
        $rep0 = lgthinq2::step0();

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' : ÉTAPE 1', __FILE__));
        $rep1 = lgthinq2::doRetry('lgthinq2::step1');
        if (!$rep1) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 1 a échoué après plusieurs tentatives.', __FILE__));
            return;
        }
        $spxLogin = json_decode($rep1, true);
        if (!$spxLogin || !isset($spxLogin['encrypted_pw'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 2 a planté ', __FILE__) . json_encode($spxLogin));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : EncryptedPw = ' . $rep1);

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' : ÉTAPE 2', __FILE__));
        $rep2 = lgthinq2::doRetry(function() use ($spxLogin) { return lgthinq2::step2($spxLogin); }, true);
        if (!$rep2) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 2 a échoué après plusieurs tentatives.', __FILE__));
            return;
        }
        $accountData = json_decode($rep2, true);
        if (!$accountData || !isset($accountData['account'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 2 a planté', __FILE__) . json_encode($accountData));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ACCOUNT INFOS = ' . json_encode($accountData['account']));
        config::save('loginSessionID', $accountData['account']['loginSessionID'], __CLASS__);
        $timeToExp = explode(';', $accountData['account']['loginSessionID'])[1];
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : START TIME = ' . $timeToExp);

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' : ÉTAPE 3', __FILE__));
        $rep3 = lgthinq2::doRetry(function() use ($accountData) { return lgthinq2::step3($accountData); });
        if (!$rep3) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __('Étape 3 a échoué après plusieurs tentatives.', __FILE__));
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
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : URL USER NUMBER = ' . $queryParams['user_number']);
                config::save('user_number', $queryParams['user_number'], __CLASS__);
            }
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Aucun paramètre d\'URL trouvé dans la clé redirect_uri.', __FILE__));
            return;
        }

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' ÉTAPE 4', __FILE__));
        $rep4 = lgthinq2::step4();
        if (!$rep4) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Étape 4 a échoué.', __FILE__));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : REPTIME = ' . $rep4);
        $time = json_decode($rep4, true);
        if (!$time || !isset($time['date'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Impossible de récupérer l\'heure.', __FILE__));
            return;
        }
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $rfc2822Date = ($time['date']?$time['date']:$dateTime->format(DateTime::RFC2822));

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' ÉTAPE 5', __FILE__));
        $rep5 = lgthinq2::step5($code, $time);
        if (!$rep5) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Étape 5 a échoué.', __FILE__));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ACCESS/REFRESH TOKENS = ' . $rep5);
        $token = json_decode($rep5, true);
        if (!$token || !isset($token['access_token'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Impossible de récupérer le token d\'accès.', __FILE__));
            return;
        }

        config::save('access_token', $token['access_token'], __CLASS__);
        config::save('expires_in', (intval($timeToExp/1000) + $token['expires_in']), __CLASS__);
        config::save('refresh_token', $token['refresh_token'], __CLASS__);
        config::save('oauth2_backend_url', $token['oauth2_backend_url'], __CLASS__);

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' ÉTAPE 6', __FILE__));
        $jsession = lgthinq2::step6();
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Jeton de session ', __FILE__) . $jsession);

        config::save('jsessionId', $jsession, __CLASS__);
    }

    /**
     * Vérifie si le token est expiré.
     *
     * @return bool Retourne true si le token est expiré et a été rafraîchi avec succès, sinon retourne false.
     */
    public static function getTokenIsExpired() {
        if (config::byKey('expires_in', __CLASS__, 0) < time()) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' refresh_token en cours, expiré depuis ', __FILE__) . (time() - config::byKey('expires_in', __CLASS__, 0)) . __(' secondes', __FILE__));
            return lgthinq2::refreshToken();
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' refresh_token à jour, il expire dans ', __FILE__) . (config::byKey('expires_in', __CLASS__, 0) - time()) . __(' secondes', __FILE__));
        return false;
    }

    /**
     * Rafraîchit le token d'accès.
     *
     * @return string|void Retourne la réponse de la requête de rafraîchissement du token d'accès si réussie, sinon rien.
     */
    public static function refreshToken() {
        $refreshToken = config::byKey('refresh_token', __CLASS__, '');
        if ($refreshToken != '') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' refresh_token en cours...', __FILE__));
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
            $rep = lgthinq2::postData('https://gb.lgeapi.com' . $urlToken, '', $headers);
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' refresh_token résultat : ', __FILE__) . $rep);
            $token = json_decode($rep, true);
            if (!$token || !isset($token['access_token'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Impossible de récupérer le token d\'accès.');
                return;
            }
            config::save('access_token', $token['access_token'], __CLASS__);
            config::save('expires_in', (time() + $token['expires_in']), __CLASS__);
            config::save('jsessionId', lgthinq2::step6(), __CLASS__); //because jessionId is related to current access_token, it needs to be asked again

            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' refresh_token effectué ', __FILE__));
            return $rep;
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Pas de refresh_token, demande de login', __FILE__));
            lgthinq2::login();
        }
    }

    /**
     * Obtient la liste des appareils.
     *
     * @param string $_deviceId L'identifiant de l'appareil (facultatif).
     * @param bool $_tokenRefreshed Indique si le token d'accès a été rafraîchi (facultatif).
     * @return void
     */
    public static function getDevices($_deviceId = '', $_tokenRefreshed = false) {
        lgthinq2::getTokenIsExpired();

        $curl = curl_init();
        $headers = lgthinq2::defaultDevicesHeaders();

        curl_setopt_array($curl, array(
            CURLOPT_URL => lgthinq2::LGTHINQ2_SERV_DEVICES . $_deviceId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

        $response = curl_exec($curl);
        curl_close($curl);
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' getDEVICES : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' erreur : '. $response);
            return;
        }
        $devices = json_decode($response, true);
        if (!$devices || !isset($devices['resultCode'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($devices['resultCode'] != '0000' && $_tokenRefreshed == false) {
            lgthinq2::getDevices($_deviceId, true);
        }

        // all devices
        if ($_deviceId == '') {
            foreach ($devices['result']['item'] as $items) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' : $items ' . json_encode($items));
                $eqLogic = lgthinq2::createEquipement($items, $items['platformType']);
                if (is_object($eqLogic) && isset($items['modelJsonUri'])) {
                    $refState = lgthinq2::deviceTypeConstantsState($eqLogic->getConfiguration('deviceType'));
                    $langProduct = $eqLogic->getLangJson('langPackProductType', $items['langPackProductTypeUri'], $items['langPackProductTypeVer']);
                    $langModel = $eqLogic->getLangJson('langPackModel', $items['langPackModelUri'], $items['langPackModelVer']);
                    if ($refState) {
                        $eqLogic->createCmdFromModelAndLangFiles($items['modelJsonUri'], $items['modelJsonVer'], $items['snapshot'][$refState], $langProduct, $langModel, $refState);
                    } else {
                        // cas où les infos sont directement sans dossier
                        $eqLogic->createCmdFromModelAndLangFiles($items['modelJsonUri'], $items['modelJsonVer'], $items['snapshot'], $langProduct, $langModel);
                    }
                }
            }
        }
    }

    /**
     * Génère un identifiant UUID.
     *
     * @param string|null $data Les données à utiliser (facultatif).
     * @return string L'identifiant UUID généré.
     */
    public static function setUUID($data = null) {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Méthode appellée par le core (moteur de tâche) cron configuré dans la fonction lgthinq2_install
     * Lance une fonction pour récupérer les appareils et une fonction pour rafraichir les commandes
     */
    public static function update() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' début', __FILE__));
        $autorefresh = config::byKey('autorefresh', __CLASS__, '');
        if ($autorefresh != '') {
            try {
                $c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
                if ($c->isDue()) {
                    try {
                        lgthinq2::getTokenIsExpired();
                        foreach (eqLogic::byType(__CLASS__) as $eqLogic) {
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
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' fin', __FILE__));
    }

    /**
     * Récupère le nom traduit à partir de la configuration.
     *
     * Cette fonction récupère le nom traduit d'un élément de configuration à partir des données de configuration fournies.
     *
     * @param string $_name Le nom à traduire.
     * @param array $_config Les données de configuration.
     * @return string Le nom traduit s'il est trouvé ; sinon, retourne le nom d'origine.
     */
    public static function getTranslatedNameFromConfig($_name, $_config) {
        if (isset($_config['MonitoringValue'][$_name]) && isset($_config['MonitoringValue'][$_name]['label'])) {
            return $_config['MonitoringValue'][$_name]['label'];
        } elseif (isset($_config['Config'])) {
            if (isset($_config['Config']['visibleItems'])) {
                foreach ($_config['Config']['visibleItems'] as $visibleItems) {
                     if ($visibleItems['feature'] == $_name) {
                         //log::add(__CLASS__, 'debug', 'TERMMMMMMM => ' . $visibleItems['monTitle']);
                         return $visibleItems['monTitle'];
                     }
                }
            }
        }
        return $_name;
    }

    /**
     * Synchronise les données avec les appareils.
     *
     * Cette fonction effectue la synchronisation des données avec les appareils.
     *
     * @return void
     */
    public static function synchronize() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' début', __FILE__));
        if (config::byKey('LGE_MEMBERS_URL', __CLASS__, '') == '' || config::byKey('LG_EMPTERMS_URL', __CLASS__, '') == '' || config::byKey('LGACC_SPX_URL', __CLASS__, '') == '') {
            $rep0 = lgthinq2::step0();
        }
        lgthinq2::getDevices();
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' fin', __FILE__));
    }

    /**
     * Charge les données de configuration à partir d'un fichier.
     *
     * Cette fonction charge les données de configuration à partir d'un fichier JSON en fonction du type fourni.
     *
     * @param string $_type Le type de données de configuration à charger.
     * @return array|null Les données de configuration chargées si elles sont réussies ; sinon, retourne null.
     */
    private static function loadConfigFile($_type) {
        log::add(__CLASS__, 'debug', __FUNCTION__ .' début' . $_type);
        $filename = __DIR__ . '/../../data/' . $_type . '.json';
        if (!file_exists($filename)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Impossible de trouver le fichier de configuration pour l\'équipement ', __FILE__));
            return;
        }
        $content = file_get_contents($filename);
        if (!is_json($content)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration ' . $filename . ' est corrompu', __FILE__));
            return;
        }
        $data = json_decode($content, true);
        if (!is_array($data)/* || !isset($data['commands'])*/) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration ' . $filename . ' est invalide', __FILE__));
            return;
        }
        return $data;
    }

    /**
     * Crée un équipement.
     *
     * Cette fonction crée un équipement en fonction des capacités et de la plateforme fournies.
     *
     * @param array $_capa Les capacités de l'équipement à créer.
     * @param mixed $_platform La plateforme de l'équipement.
     * @return lgthinq2|null L'équipement créé si la création réussit ; sinon, retourne null.
     */
    public static function createEquipement($_capa, $_platform) {
        log::add(__CLASS__, 'debug', __FUNCTION__ .' début' . json_encode($_capa));
        if (!isset($_capa['deviceId'])) {
            log::add(__CLASS__, 'error', __FUNCTION__ . __(' erreur uuid inexistant ', __FILE__) . json_encode($_capa));
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
            event::add('jeedom::alert', array(
                'level' => 'success',
                'page' => __CLASS__,
                'message' => __("L'équipement ", __FILE__) . $eqLogic->getHumanName() . __(" vient d'être créé", __FILE__),
            ));
        }
        if (isset($_capa['deviceType'])) {
            $eqLogic->setConfiguration('deviceType', $_capa['deviceType']);
        }
        if (isset($_capa['deviceCode'])) {
            $eqLogic->setConfiguration('deviceCode', $_capa['deviceCode']);
            $eqLogic->setConfiguration('deviceCodeName', lgthinq2::deviceTypeCodeConstants($_capa['deviceCode']));
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
        if (isset($_platform)) {
            $eqLogic->setConfiguration('platformType', $_platform);
        }
        $eqLogic->save();
        return $eqLogic;
    }

    /**
     * Obtient le statut des appareils.
     *
     * @param bool $_repeat Indique s'il faut répéter la requête (facultatif).
     * @return void
     */
    public function getDevicesStatus($_repeat = false) {
        lgthinq2::getTokenIsExpired();
        $timestamp = null;
        $platformType = $this->getConfiguration('platformType');
        $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getLogicalId());
        //if thinq1
        if ($platformType == 'thinq1') {
            if ($this->getConfiguration('workId', '') == '' && $this->getConfiguration('needRtiControl', false) == false) {
                $this->changeMonitorStatus('Stop');
                $this->changeMonitorStatus('Start');
            }
            if ($this->getConfiguration('needRtiControl', false)) {
                $data = $this->getDeviceRtiControl('Config', 'Get', 'FuncSync');
            } else {
                $data = $this->pollMonitorStatus($_repeat);
            }

            $monitoring = $this->getConfiguration('Monitoring', '');
            if (isset($data) && is_array($data)) {
                foreach ($data as $dkey => $dvalue) {
                    if ($monitoring != '') {
                        $logicalid = array_search($dkey, $monitoring);
                    } else {
                        $logicalid = $dkey;
                    }
                    if ($logicalid !== false) {
                        if (!is_object($this->getCmd('info', $logicalid))) {
                            $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $logicalid);
                        }
                        $this->checkValueAndUpdateCmd($logicalid, $dvalue, $timestamp);
                    }
                }
            }
            return;
        }
        //else

        $curl = curl_init();
        $headers = lgthinq2::defaultDevicesHeaders();

        curl_setopt_array($curl, array(
            CURLOPT_URL => lgthinq2::LGTHINQ2_SERV_DEVICES . $this->getLogicalId(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

        $response = curl_exec($curl);
        curl_close($curl);
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' getDEVICES : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' erreur : ', __FILE__) . $response);
            return;
        }
        $devices = json_decode($response, true);
        if (!$devices || !isset($devices['resultCode'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($devices['resultCode'] != '0000' && $_tokenRefreshed == false) {
            lgthinq2::getDevices($_deviceId, true);
        }

        $modelJson = false;
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/FAY_'.$this->getLogicalId().'.json'),true); // developper only
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/PAC.json'),true); // developper only

        if (isset($devices['result']['snapshot'])) {
            $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getLogicalId());
            if (!is_object($this->getCmd('info', 'online'))) {
                $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, 'online');
            }
            if (isset($devices['result']['snapshot']['timestamp'])) {
                $timestamp = date('Y-m-d H:i:s', ($devices['result']['snapshot']['timestamp']/1000));
            }
            $this->checkAndUpdateCmd('online', $devices['result']['online'], $timestamp);
            $refState = lgthinq2::deviceTypeConstantsState($this->getConfiguration('deviceType'));
            if ($refState) {
                $data = $devices['result']['snapshot'][$refState];
            } else {
                $data = $devices['result']['snapshot'];
            }
            foreach ($data as $refStateId => $refStateValue) {
                if (!is_object($this->getCmd('info', $refStateId))) {
                    $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $refStateId);
                }
                $this->checkValueAndUpdateCmd($refStateId, $refStateValue, $timestamp);
            }
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : $devices  ' . json_encode($devices));
    }

    /**
     * Change le statut du moniteur.
     *
     * @param string $_action L'action à effectuer (Stop ou Start).
     * @return void
     */
    public function changeMonitorStatus($_action) {
        $headers = lgthinq2::defaultDevicesEmpHeaders();
        $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', __CLASS__, lgthinq2::step6());

        $data = array(
            lgthinq2::DATA_ROOT => array(
                'cmd' => 'Mon',
                'cmdOpt' => $_action,
                'deviceId' => $this->getLogicalId(),
                'workId' => $this->getConfiguration('workId', lgthinq2::setUUID())
            )
        );
        //$headers[] = 'Content-Length: ' . strlen(json_encode($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' URL : ', __FILE__) . lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiMon' );
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DATA : ', __FILE__) . json_encode($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' HEADERS : ', __FILE__) . json_encode($headers));

        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiMon', json_encode($data), $headers);

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' RESPONSE : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' erreur : ', __FILE__) . $response);
            return;
        }
        $work = json_decode($response, true);
        if (!$work || !isset($work[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de la requête  ', __FILE__) . json_encode($work));
            return;
        }
        if (isset($work[lgthinq2::DATA_ROOT]['returnCd']) && $work[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de code ', __FILE__) . $work[lgthinq2::DATA_ROOT]['returnCd'] . ' ' . $work[lgthinq2::DATA_ROOT]['returnMsg']);
            if ($work[lgthinq2::DATA_ROOT]['returnCd'] == '0102') {
                config::save('jsessionId', lgthinq2::step6(), __CLASS__);
            }
            return;
        }
        if (isset($work[lgthinq2::DATA_ROOT]['workId'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Requête réussie ', __FILE__) . json_encode($work));
            $this->setConfiguration('workId', $work[lgthinq2::DATA_ROOT]['workId'])->save();
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' workId non présent ', __FILE__) . json_encode($work));
            $this->setConfiguration('workId', '')->save();
            $this->setConfiguration('needRtiControl', true)->save();
        }
    }

    /**
     * Interroge périodiquement le statut du moniteur.
     *
     * @param bool $_repeat Indique s'il faut répéter la requête (facultatif).
     * @return mixed|null Retourne les données obtenues ou null en cas d'erreur.
     */
    public function pollMonitorStatus($_repeat = false) {
        $headers = lgthinq2::defaultDevicesEmpHeaders();
        $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', __CLASS__, lgthinq2::step6());

        $data = array(
            lgthinq2::DATA_ROOT => array(
                'workList' => array(
                    array(
                        'deviceId' => $this->getLogicalId(),
                        'workId' => $this->getConfiguration('workId') // workId already check in last method
                    )
                )
            )
        );
        //$headers[] = 'Content-Length: ' . strlen(http_build_query($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' URL : ', __FILE__) . lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiResult' );
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DATA : ', __FILE__) . json_encode($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' HEADERS : ', __FILE__) . json_encode($headers));

        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiResult', json_encode($data, JSON_PRETTY_PRINT), $headers);
        //$response = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/OTH_'.$this->getLogicalId().'.json'),true); // developper only
        //log::add(__CLASS__, 'debug', __FUNCTION__ . __(' response : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' erreur : ', __FILE__) . $response);
            return;
        }
        $rti = json_decode($response, true);
        if (!$rti || !isset($rti[lgthinq2::DATA_ROOT]['returnCd'])) {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($rti[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de code ', __FILE__) . $rti[lgthinq2::DATA_ROOT]['returnCd'] . ' ' . $rti[lgthinq2::DATA_ROOT]['returnMsg']);
            if ($rti[lgthinq2::DATA_ROOT]['returnCd'] == '0102' && $_repeat == false) {
                config::save('jsessionId', lgthinq2::step6(), __CLASS__);
                return lgthinq2::pollMonitorStatus(true);
            }
            return;
        }
        if (!isset($rti[lgthinq2::DATA_ROOT]['workList'])) {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' WorkList non existant ', __FILE__) . json_encode($rti));
            return;
        }

        if (!isset($rti[lgthinq2::DATA_ROOT]['workList']['returnCode']) || $rti[lgthinq2::DATA_ROOT]['workList']['returnCode'] == '0106') {
            $nbDisconnects = (int)$this->getConfiguration('nbDisconnections', 0);
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' returnCode null ou 0106, $nbDisconnects ', __FILE__) . $nbDisconnects);
            if ($nbDisconnects >= 3) {
                $this->setConfiguration('workId', '');
                $this->setConfiguration('nbDisconnections', 0)->save();
                $this->getDevicesStatus(true);
            } else {
                $this->setConfiguration('nbDisconnections', $nbDisconnects + 1)->save();
            }
        }

        /*if (!isset($rti[lgthinq2::DATA_ROOT]['workList']['returnCode']) && isset($rti[lgthinq2::DATA_ROOT]['workList']['stateCode'])) {
            if (in_array($rti[lgthinq2::DATA_ROOT]['workList']['stateCode'], array('P','W','F','N')) && $_repeat == false) { // E? N?
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : returnCode non existant ' . json_encode($rti));
                $this->setConfiguration('workId', '')->save();
                $this->getDevicesStatus(true);
                return;
            }
        }*/
        if (isset($rti[lgthinq2::DATA_ROOT]['workList']['returnCode']) && $rti[lgthinq2::DATA_ROOT]['workList']['returnCode'] != '0000') {
            if ($rti[lgthinq2::DATA_ROOT]['workList']['returnCode'] == '0100' && $_repeat == false) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' returnCode non existant ', __FILE__) . json_encode($rti));
                $this->setConfiguration('workId', '')->save();
                $this->getDevicesStatus(true);
            }
            return;
        }
        if (isset($rti[lgthinq2::DATA_ROOT]['workList']['returnData']) && $rti[lgthinq2::DATA_ROOT]['workList']['format'] == 'B64') {
            $this->setConfiguration('nbDisconnections', 0); // reset nb disconnections
            return json_decode(base64_decode($rti[lgthinq2::DATA_ROOT]['workList']['returnData']), true);
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Requête réussie ', __FILE__) . json_encode($reData));
        }
    }

    /**
     * Obtient le contrôle RTI de l'appareil.
     *
     * @param string $_cmd La commande.
     * @param string $_cmdOpt L'option de commande.
     * @param string $_value La valeur.
     * @return array|null Retourne les données obtenues ou null en cas d'erreur.
     */
    public function getDeviceRtiControl($_cmd, $_cmdOpt, $_value) {
        $headers = lgthinq2::defaultDevicesEmpHeaders();
        $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', __CLASS__, lgthinq2::step6());

        $data = array(
            lgthinq2::DATA_ROOT => array(
                'cmd' => $_cmd,
                'cmdOpt' => $_cmdOpt,
                'deviceId' => $this->getLogicalId(),
                'value' => $_value,
                'workId' => lgthinq2::setUUID()
            )
        );
        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiControl', json_encode($data, JSON_PRETTY_PRINT), $headers);

        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' response : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' erreur : ', __FILE__) . $response);
            return;
        }
        $rti = json_decode($response, true);
        if (!$rti || !isset($rti[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($rti[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Erreur de code ', __FILE__) . json_encode($rti));
            return;
        }
        if (isset($rti[lgthinq2::DATA_ROOT]['returnData']) && $rti[lgthinq2::DATA_ROOT]['format'] == 'B64') {
            $reData = json_decode(base64_decode($rti[lgthinq2::DATA_ROOT]['returnData']), true);
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Requête réussie ', __FILE__) . json_encode($reData));
            return $reData;
        }
    }

    /**
     * Méthode appellée avant la création de l'objet
     * Active et affiche l'objet
     */
    public function preInsert() {
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    /**
     * Méthode appellée après la création de l'objet
     * Ajoute la commande refresh
     */
    public function postInsert() {
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
        $cmdOnline = $this->getCmd('info', 'online');
        if (!is_object($cmdOnline)) {
            $cmdOnline = new lgthinq2Cmd();
            $cmdOnline->setEqLogic_id($this->getId());
            $cmdOnline->setLogicalId('online');
            $cmdOnline->setName(__("Connecté", __FILE__));
            $cmdOnline->setType('info');
            $cmdOnline->setSubType('binary');
            $cmdOnline->save();
        }
    }

    /**
     * Rafraîchit les données.
     */
    public function refresh() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' début', __FILE__));
        $this->getDevicesStatus();
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' fin', __FILE__));
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

    /**
     * Obtient les données JSON de langue.
     *
     * @param string $_type Type de fichier JSON de langue ('langPackProductType' ou 'langPackModel').
     * @param string $_langFileUri URI du fichier JSON de langue.
     * @param string $_langFileVer Version du fichier JSON de langue.
     * @return array|false Les données JSON de langue ou false en cas d'erreur.
     */
    public function getLangJson($_type, $_langFileUri = '', $_langFileVer) {
        $curVersion = $this->getConfiguration($_type . 'Ver', '');
        $file = __DIR__ . '/../../data/' . $this->getLogicalId() . '_' . $_type . '.json';
        if ($curVersion != '' && version_compare($curVersion, $_langFileVer, '>=')) {
            $config = file_get_contents($file);
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier existe à la version ', __FILE__) . $curVersion);
        } else {
            if ($_langFileUri == '') {
                return false;
            }
            $config = file_get_contents($_langFileUri);
            file_put_contents($file, $config);
            $this->setConfiguration($_type . 'Ver', $_langFileVer)->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier existe pas ', __FILE__) . $curVersion);
        }
        if (!is_json($config)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de langue est corrompu', __FILE__));
            return false;
        }
        $data = json_decode($config, true);
        if (!is_array($data)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de langue est invalide', __FILE__));
            return false;
        }
        if (!isset($data['pack'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . __(' "Pack" n\'existe pas dans fichier de langue', __FILE__));
            return false;
        }
        //log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Fichier de langue', __FILE__) . json_encode($data['pack']));
        return $data['pack'];
    }

    /**
     * Crée des commandes à partir de fichiers JSON de modèle et de langue.
     *
     * @param string $_modelJsonUri URI du fichier JSON de modèle.
     * @param string $_modelJsonVer Version du fichier JSON de modèle.
     * @param array $_refState État de référence.
     * @param array $_configProductLang Configuration de la langue du produit.
     * @param array $_configModelLang Configuration de la langue du modèle.
     * @param mixed|null $refState État de référence.
     * @return false
     */
    public function createCmdFromModelAndLangFiles($_modelJsonUri, $_modelJsonVer, $_refState, $_configProductLang, $_configModelLang, $refState = null) {
        if ($_modelJsonUri != '') {
            $curVersion = $this->getConfiguration('modelJsonVer', '0.0');
            $file = __DIR__ . '/../../data/' . $this->getLogicalId() . '_modelJson.json';
            if (version_compare($curVersion, $_modelJsonVer, '>=')) {
                $config = file_get_contents($file);
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier modelJson existe à la version ', __FILE__) . $curVersion);
            } else {
                $config = file_get_contents($_modelJsonUri);
                file_put_contents($file, $config);
                $this->setConfiguration('modelJsonVer', $_modelJsonVer)->save();
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier modelJson existe pas ', __FILE__) . $curVersion);
            }
            if (!is_json($config)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration est corrompu', __FILE__));
            }
            $data = json_decode($config, true);
            if (!is_array($data)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' Le fichier de configuration est invalide', __FILE__));
            }

            file_put_contents(__DIR__ . '/../../data/' . $this->getLogicalId() . '.json', json_encode($data));

            if ($_configModelLang && is_array($_configModelLang)) {
                $langPack = array_replace_recursive($_configProductLang, $_configModelLang);
            } else {
                $langPack = $_configProductLang;
            }
            $translation = new lgthinq2_customLang();
            $customLangFile = $translation->customlang;
            $langPack = array_replace_recursive($langPack, $customLangFile);
            //$langPack = $this->getLangJson('langPackProductType', '', '0.0');

            if (isset($data['Value'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG Value ', __FILE__) . json_encode($data['Value']));
                $commands = array();
                foreach ($data['Value'] as $key => $value) {
                    if ($this->getConfiguration('platformType') == 'thinq2' && !isset($_refState[$key])) continue; // s'il n'y a pas de commande info dans refState
                    $minValue = null;
                    $maxValue = null;
                    $step = null;
                    $unite = null;
                    $targetKey = null;
                    $targetKeyValues = null;
                    $tempUnitValue = null;
                    $historized = 0;

                    // subtype
                    if ($value['data_type'] == 'enum') {
                        if (isset($value['value_mapping']) && count($value['value_mapping']) == 2) {
                            $subType = 'binary';
                            $historized = 1;
                        } else {
                            $subType = 'string';
                        }
                        $OGtype = $value['data_type'];
                    } elseif ($value['type'] == 'Enum') {
                        if (isset($value['option']) && count($value['option']) == 2) {
                            $subType = 'binary';
                            $historized = 1;
                        } else {
                            $subType = 'string';
                        }
                        $OGtype = $value['type'];
                    } elseif ($value['data_type'] == 'Boolean' || $value['type'] == 'Boolean') {
                        $subType = 'binary';
                        $historized = 1;
                        $OGtype = 'Boolean';
                    } elseif ($value['data_type'] == 'range') {
                        $historized = 1;
                        $subType = 'numeric';
                        $minValue = $value['value_validation']['min'];
                        $maxValue = $value['value_validation']['max'];
                        $step = $value['value_validation']['step'];
                        if (isset($_refState['tempUnit'])) {// airState.tempState.unit ??!!
                            $unite = $_refState['tempUnit']=='CELSIUS'?'°C':'°F';
                        }
                        $OGtype = $value['data_type'];
                    } elseif ($value['data_type'] == 'Range') {
                        $historized = 1;
                        $subType = 'numeric';
                        $minValue = $value['option']['min'];
                        $maxValue = $value['option']['max'];
                        $step = $value['option']['step'];
                        if (isset($_refState['tempUnit'])) { // and thinq1 ?
                            $unite = $_refState['tempUnit']=='CELSIUS'?'°C':'°F';
                        }
                        $OGtype = $value['data_type'];
                    } elseif ($value['data_type'] == 'number' || $value['type'] == 'Number') {
                        $subType = 'numeric';
                        $historized = 1;
                        $OGtype = 'Number';
                    } elseif ($value['data_type'] == 'string' || $value['type'] == 'String') {
                        $subType = 'string';
                        $OGtype = 'String';
                    } elseif ($value['type'] == 'Array') {
                        $subType = 'string';
                        $OGtype = $value['type'];
                        //go to child
                    } else {
                        $subType = 'string';
                        $OGtype = 'Other';
                    }

                    //name
                    $name = lgthinq2::getTranslatedNameFromConfig($key, $data);
                    if (isset($_configProductLang[$name]) && $_configProductLang[$name] != '') {
                        $name = $_configProductLang[$name];
                    } elseif (isset($_configModelLang[$name]) && $_configModelLang[$name] != '') {
                        $name = $_configModelLang[$name];
                    } else {
                        $name = $key;
                    }
                    $name = str_replace('Set', '', $name);

                    $commands[] = array(
                        'name' => $name,
                        'logicalId' => $key,
                        'subType' => $subType,
                        'unite' => $unite,
                        'isHistorized' => $historized,
                        'configuration' => array(
                            'minValue' => $minValue,
                            'maxValue' => $maxValue,
                            'default' => $value['default'],
                            'visibleItem' => $value['value_validation'],
                            'valueMapping' => $value['value_mapping'] ?? $value['option'],
                            'targetKey' => $targetKey,
                            'targetKeyValues' => $targetKeyValues,
                            'tempUnitValue' => $tempUnitValue,
                            'originalType' => $OGtype
                        ),
                        'display' => array(
                            'parameters' => array(
                                'step' => $step
                            )
                        )
                    );
                }
                /*$commands = array_filter($commands, function($command) use ($commandsToRemove) {
                    return !in_array($command['logicalId'], $commandsToRemove);
                });*/
                foreach ($commands as $cmd) {
                    $this->createCommand($cmd);
                }
               // return true;
            }
            if (isset($data['MonitoringValue'])) {

                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG MonitoringValue ', __FILE__) . json_encode($data['MonitoringValue']));
                $commands = array();
                $commandsToRemove = array();
                foreach ($data['MonitoringValue'] as $key => $value) {
                    if (!isset($_refState[$key])) continue; // s'il n'y a pas de commande info dans refState
                    $minValue = null;
                    $maxValue = null;
                    $step = null;
                    $unite = null;
                    $targetKey = null;
                    $targetKeyValues = null;
                    $tempUnitValue = null;
                    $historized = 0;

                    // subtype
                    if ($value['dataType'] == 'enum') {
                        if (isset($value['visibleItem']['monitoringIndex']) && count($value['visibleItem']['monitoringIndex']) == 2) {
                            $subType = 'binary';
                            $historized = 1;
                        } elseif (isset($value['valueMapping']) && count($value['valueMapping']) == 2) {
                            $subType = 'binary';
                            $historized = 1;
                        } else {
                            $subType = 'string';
                        }
                    } elseif ($value['dataType'] == 'Boolean') {
                        $subType = 'binary';
                        $historized = 1;
                    } elseif ($value['dataType'] == 'range') {
                        $historized = 1;
                        $subType = 'numeric';
                        $minValue = $value['valueMapping']['min'];
                        $maxValue = $value['valueMapping']['max'];
                        $step = $value['valueMapping']['step'];
                        if (isset($_refState['tempUnit'])) {
                            $unite = $_refState['tempUnit']=='CELSIUS'?'°C':'°F';
                        }
                    } elseif ($value['dataType'] == 'number') {
                        $historized = 1;
                        $subType = 'numeric';
                    } elseif ($value['dataType'] == 'string') {
                        $subType = 'other';
                    } else {
                        $subType = 'string';
                    }

                    //name
                    $name = lgthinq2::getTranslatedNameFromConfig($key, $data);
                    if (isset($_configProductLang[$name]) && $_configProductLang[$name] != '') {
                        $name = $_configProductLang[$name];
                    } elseif (isset($_configModelLang[$name]) && $_configModelLang[$name] != '') {
                        $name = $_configModelLang[$name];
                    } else {
                        $name = $key;
                    }

                    //unit and minValue&maxValue
                    if (isset($value['targetKey'])) {
                        $targetKey = $value['targetKey'];
                        if (isset($targetKey['tempUnit']) && count($targetKey['tempUnit']) > 1) {
                            if (isset($targetKey['tempUnit'][$_refState['tempUnit']])) {
                                $tempUnitValue = $targetKey['tempUnit'][$_refState['tempUnit']];

                                if (isset($data['MonitoringValue'][$tempUnitValue])) {
                                    //supprimer cette commande contenue dans targetKey
                                    $targetKeyValues[$tempUnitValue] = $data['MonitoringValue'][$tempUnitValue]['valueMapping'];
                                    $lastValue = null;
                                    $minValue = null;
                                    $maxValue = null;
                                    foreach ($data['MonitoringValue'][$tempUnitValue]['valueMapping'] as $keyMap => $valMap) {
                                        $label = $valMap['label'];
                                        if (is_numeric($label)) {
                                            $currentValue = intval($label);
                                            if ($lastValue === null || $currentValue === $lastValue + 1) {
                                                $minValue = ($minValue === null) ? $currentValue : $minValue;
                                                $maxValue = $currentValue;
                                            } elseif ($lastValue === null || $currentValue === $lastValue - 1) {
                                                $minValue = $currentValue;
                                                $maxValue = ($maxValue === null) ? $currentValue : $maxValue;
                                            } else {
                                                $minValue = $maxValue = $currentValue;
                                            }
                                            $lastValue = $currentValue;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $commands[] = array(
                        'name' => $name,
                        'logicalId' => $key,
                        'subType' => $subType,
                        'unite' => $unite,
                        'isHistorized' => $historized,
                        'configuration' => array(
                            'minValue' => $minValue,
                            'maxValue' => $maxValue,
                            'default' => $value['default'],
                            'visibleItem' => $value['visibleItem'],
                            'valueMapping' => $value['valueMapping'],
                            'targetKey' => $targetKey,
                            'targetKeyValues' => $targetKeyValues,
                            'tempUnitValue' => $tempUnitValue
                        ),
                        'display' => array(
                            'parameters' => array(
                                'step' => $step
                            )
                        )
                    );
                }

                foreach ($commands as $cmd) {
                    $this->createCommand($cmd);
                }
            }

            if (isset($data['Monitoring'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG Monitoring ', __FILE__) . json_encode($data['Monitoring']));
                if (isset($data['Monitoring']['type']) && $data['Monitoring']['type'] == 'JSON') {
                    if (isset($data['Monitoring']['protocol'])) {
                        $monit = array();
                        foreach ($data['Monitoring']['protocol'] as $protocol) {
                            $monit[$protocol['value']] = $protocol['path'];
                        }
                        $this->setConfiguration('Monitoring', $monit)->save();
                    }
                }
            }

            if (isset($data['ControlWifi'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG ControlWifi ', __FILE__) . json_encode($data['ControlWifi']));
                $commands = array();
                if (isset($data['ControlWifi']['type']) && $data['ControlWifi']['type'] == 'JSON' && isset($data['ControlWifi']['action'])) {
                    foreach ($data['ControlWifi']['action'] as $actionName => $actionConfig) {
                        $listValue = null;
                        $subType = 'other';
                        $updateCmdToValue = null;
                        if (preg_match('/{{(.*?)}}/', $actionConfig['value'], $matches)) {
                            //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value0 ' . $matches[1]);
                            if (isset($data['Value'][$matches[1]])) {
                                //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value1 ' . $matches[1]);
                                if ($data['Value'][$matches[1]]['type'] == 'String') {
                                    $subType = 'message';
                                    $updateCmdToValue = '#message#';
                                    $actionConfig['value'] = str_replace('{{'.$matches[1].'}}', '#message#', $actionConfig['value']);
                                } elseif ($data['Value'][$matches[1]]['type'] == 'Enum') {
                                    $subType = 'select';
                                    $updateCmdToValue = '#select#';
                                    if (isset($data['Value'][$matches[1]]['option'])) {
                                        foreach ($data['Value'][$matches[1]]['option'] as $optionKey => $optionValue) {
                                            if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                if (isset($langPack[$optionValue])) {
                                                    $optionValue = $langPack[$optionValue];
                                                }
                                            }
                                            $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                        }
                                        $listValue = substr($listValue, 0, -1);
                                    }
                                    $actionConfig['value'] = str_replace('{{'.$matches[1].'}}', '#select#', $actionConfig['value']);

                                } elseif ($data['Value'][$matches[1]]['type'] == 'Range') {
                                    $subType = 'slider';
                                    $updateCmdToValue = '#slider#';
                                    $actionConfig['value'] = str_replace('{{'.$matches[1].'}}', '#slider#', $actionConfig['value']);
                                }
                                //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value3 ' . $matches[1]);
                            }
                        } elseif (preg_match('/{(.*?)}/', $actionConfig['value'], $matches)) {
                            if (isset($data['Value'][$matches[1]])) {
                                //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value1 ' . $matches[1]);
                                if ($data['Value'][$matches[1]]['type'] == 'String') {
                                    $subType = 'message';
                                    $updateCmdToValue = '#message#';
                                    $actionConfig['value'] = str_replace('{'.$matches[1].'}', '#message#', $actionConfig['value']);
                                } elseif ($data['Value'][$matches[1]]['type'] == 'Enum') {
                                    $subType = 'select';
                                    $updateCmdToValue = '#select#';
                                    if (isset($data['Value'][$matches[1]]['option'])) {
                                        foreach ($data['Value'][$matches[1]]['option'] as $optionKey => $optionValue) {
                                            if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                if (isset($langPack[$optionValue])) {
                                                    $optionValue = $langPack[$optionValue];
                                                }
                                            }
                                            $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                        }
                                        $listValue = substr($listValue, 0, -1);
                                    }
                                    $actionConfig['value'] = str_replace('{'.$matches[1].'}', '#select#', $actionConfig['value']);

                                } elseif ($data['Value'][$matches[1]]['type'] == 'Range') {
                                    $subType = 'slider';
                                    $updateCmdToValue = '#slider#';
                                    $actionConfig['value'] = str_replace('{'.$matches[1].'}', '#slider#', $actionConfig['value']);
                                }
                                //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value3 ' . $matches[1]);
                            }
                        }

                        if ($actionConfig['cmdOpt'] == 'Get') {
                            $nameCKey = str_replace('Get', '', $actionName, $iCKey);
                            $commands[] = array(
                                'name' => $nameCKey,
                                'type' => 'info',
                                'logicalId' => $actionName,
                                'subType' => 'string',
                                'configuration' => array(
                                    'cmd' => $actionConfig['cmd'],
                                    'cmdOpt' => $actionConfig['cmdOpt'],
                                    'value' => $actionConfig['value'],
                                    'encode' => $actionConfig['encode'],
                                    'listValue' => $listValue
                                )
                            );
                        }
                        $commands[] = array(
                            'name' => ($iCKey?$actionName:$actionName.config::genKey(2)),
                            'type' => 'action',
                            'logicalId' => $actionName,
                            'subType' => $subType,
                            'configuration' => array(
                                'cmd' => $actionConfig['cmd'],
                                'cmdOpt' => $actionConfig['cmdOpt'],
                                'value' => $actionConfig['value'],
                                'encode' => $actionConfig['encode'],
                                'listValue' => $listValue,
                                'updateLGCmdToValue' => $updateCmdToValue
                            )
                        );
                    }
                } else {
                    //log::add(__CLASS__, 'debug', 'ELSE CONTROLWIFI match value0 ');
                    foreach ($data['ControlWifi'] as $controlKey => $controlValue) {
                        if ($controlKey == 'basicCtrl') {
                    //log::add(__CLASS__, 'debug', 'ELSE CONTROLWIFI match value1 '. $controlKey);
                            if (isset($controlValue['data']) && isset($controlValue['data'][$refState])) {
                                foreach ($controlValue['data'][$refState] as $cmdKey => $cmdVal) {
                                    $listValue = null;
                                    $subType = 'other';
                                    $updateCmdToValue = null;
                                    if (preg_match('/{{(.*?)}}/', $cmdVal, $matches)) {
                                        //log::add(__CLASS__, 'debug', 'ELSE CONTROLWIFI match value2 ' . $matches[1]);
                                        if (isset($data['MonitoringValue'][$matches[1]])) {
                                            //log::add(__CLASS__, 'debug', 'ELSE CONTROLWIFI match value3 ' . $matches[1]);
                                            if ($data['MonitoringValue'][$matches[1]]['dataType'] == 'string') {
                                                $subType = 'message';
                                                $updateCmdToValue = '#message#';
                                                $cmdVal = str_replace('{{'.$matches[1].'}}', '#message#',$cmdVal);
                                            } elseif ($data['MonitoringValue'][$matches[1]]['dataType'] == 'enum') {
                                                $subType = 'select';
                                                $updateCmdToValue = '#select#';
                                                if (isset($data['MonitoringValue'][$matches[1]]['valueMapping'])) {
                                                    foreach ($data['MonitoringValue'][$matches[1]]['valueMapping'] as $optionKey => $optionValue) {
                                                        if (is_array($langPack) && isset($optionValue['label']) && (strpos($optionValue['label'], '@') === 0)) {
                                                            if (isset($langPack[$optionValue['label']])) {
                                                                $optionValue['label'] = $langPack[$optionValue['label']];
                                                            }
                                                        }
                                                        $listValue .= str_replace('|','-', $optionKey) . '|' . ($optionValue['label']!=''?$optionValue['label']:$optionValue['index']) . ';';
                                                    }
                                                    $listValue = substr($listValue, 0, -1);
                                                }

                                            } elseif ($data['MonitoringValue'][$matches[1]]['dataType'] == 'range') {
                                                $subType = 'slider';
                                                $updateCmdToValue = '#slider#';
                                            }
                                            //log::add(__CLASS__, 'debug', 'ELSE CONTROLWIFI match value ' . $data['ControlWifi'][$matches[1]]);
                                        }
                                    }
                                    $commands[] = array(
                                        'name' => $controlValue['command'] . $cmdKey,
                                        'type' => 'action',
                                        'logicalId' => $controlValue['command'] . $cmdKey,
                                        'subType' => $subType,
                                        'configuration' => array(
                                            'ctrlKey' => $controlKey,
                                            'cmd' => $controlValue['command'],
                                            'cmdOpt' => $actionConfig['cmdOpt'],
                                            'value' => $actionConfig['value'],
                                            'encode' => $actionConfig['encode'],
                                            'listValue' => $listValue,
                                            'updateLGCmdToValue' => $updateCmdToValue

                                        )
                                    );
                                }
                            }
                        } else {
                            if ($controlValue['command'] == 'Get') {
                                $nameCKey = str_replace('Get', '', $controlKey, $iCKey);
                                $commands[] = array(
                                    'name' => $nameCKey,
                                    'type' => 'info',
                                    'logicalId' => $controlKey,
                                    'subType' => 'string',
                                    'configuration' => array(
                                        'ctrlKey' => $controlKey,
                                        'cmd' => $controlValue['command']
                                    )
                                );
                            }
                            $commands[] = array(
                                'name' => ($iCKey?$controlKey:$controlKey.config::genKey(2)),
                                'type' => 'action',
                                'logicalId' => $controlKey,
                                'subType' => 'other',
                                'configuration' => array(
                                    'ctrlKey' => $controlKey,
                                    'cmd' => $controlValue['command'],
                                )
                            );
                        }
                    }
                }
                foreach ($commands as $cmd) {
                    $this->createCommand($cmd);
                }
            }
            if (isset($data['ControlDevice'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG ControlDevice ', __FILE__) . json_encode($data['ControlDevice']));
                $commands = array();
                foreach ($data['ControlDevice'] as $controlDeviceValue) {
                    //log::add(__CLASS__, 'debug', 'ControlDeviceControlDeviceControlDevice  $commands ' . json_encode($controlDeviceValue));
                    $cmdtypes = explode('|', $controlDeviceValue['command']);
                    $datakeytypes = explode('|', $controlDeviceValue['dataKey']);
                    $valuetypes = explode('|', $controlDeviceValue['dataValue']);
                    $nbdatakeys = count($datakeytypes);

                    foreach ($cmdtypes as $cmdtype) { //each Get/Set/Stop/Start/Operation...
                        if ($cmdtype == 'Get') {
                            $listValue = '';
                            foreach ($datakeytypes as $key) {
                                $listValue .= str_replace('|','-', $key) . '|' . $key . ';';
                            }
                            $listValue = substr($listValue, 0, -1);

                            $commands[] = array(
                                'name' => $cmdtype . ' ' . $controlDeviceValue['ctrlKey'],
                                'type' => 'action',
                                'logicalId' => $cmdtype . $controlDeviceValue['ctrlKey'],
                                'subType' => ($nbdatakeys==1?'other':'select'),
                                'configuration' => array(
                                    'ctrlKey' => $controlDeviceValue['ctrlKey'],
                                    'cmd' => $cmdtype,
                                    'dataKey' => $datakeytype,
                                    'listValue' => ($nbdatakeys==1?null:$listValue),
                                    'updateLGCmdToValue' => ($nbdatakeys==1?null:'#select#')
                                )
                            );
                        } else {
                            foreach ($datakeytypes as $key) {
                                $listValue = null;
                                $step = null;
                                $minValue = null;
                                $maxValue = null;
                                $updateCmdToValue = null;
                                $subType = 'other';
                                if (isset($data['Value'][$key])) {
                                    if ($data['Value'][$key]['data_type'] == 'enum') {
                                        $subType = ($nbdatakeys==1?'other':'select');
                                        $updateCmdToValue = ($nbdatakeys==1?null:'#select#');
                                        $listValue = '';
                                        foreach ($data['Value'][$key]['value_mapping'] as $optionKey => $optionValue) {
                                            if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                if (isset($langPack[$optionValue])) {
                                                    $optionValue = $langPack[$optionValue];
                                                }
                                            }
                                            $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                        }
                                        $listValue = substr($listValue, 0, -1);
                                    } elseif ($data['Value'][$key]['data_type'] == 'range') {
                                        $subType = 'slider';
                                        $updateCmdToValue = '#slider#';
                                        $minValue = $data['Value'][$key]['value_validation']['min'];
                                        $maxValue = $data['Value'][$key]['value_validation']['max'];
                                        $step = $data['Value'][$key]['value_validation']['step'];
                                    }
                                }
                                $commands[] = array(
                                    'name' => $cmdtype . ' ' . $controlDeviceValue['ctrlKey'] . ' ' . $key,
                                    'type' => 'action',
                                    'logicalId' => $cmdtype . $controlDeviceValue['ctrlKey'] . $key,
                                    'subType' => $subType,
                                    'configuration' => array(
                                        'ctrlKey' => $controlDeviceValue['ctrlKey'],
                                        'cmd' => $cmdtype,
                                        'dataKey' => $key,
                                        'dataGetList' => $controlDeviceValue['dataGetList']??null,
                                        'listValue' => $listValue,
                                        'minValue' => $minValue,
                                        'maxValue' => $maxValue,
                                        'updateLGCmdToValue' => $updateCmdToValue
                                    ),
                                    'display' => array(
                                        'parameters' => array(
                                            'step' => $step
                                        )
                                    )
                                );
                            }
                        }
                    }
                }
                foreach ($commands as $cmd) {
                    $this->createCommand($cmd);
                }
            }
        }
        foreach ($this->getCmd('action') as $actCmd) {
            if (is_object($cmdInfo = $this->getCmd('info', lgthinq2::replaceBeginString('Set', $actCmd->getLogicalId())))) {
                $actCmd->setConfiguration('updateLGCmdId', $cmdInfo->getId());
                $actCmd->setValue($cmdInfo->getId())->save();
            }
        }
        return false;
    }

    /**
     * Vérifie la valeur et met à jour la commande.
     *
     * Cette fonction vérifie la valeur et met à jour une commande en fonction de l'ID d'état, du tableau de valeurs d'état et du timestamp fournis.
     *
     * @param mixed $refStateId L'ID d'état de référence.
     * @param mixed $refStateValueArray Le tableau de valeurs d'état de référence.
     * @param mixed $timestamp Le timestamp.
     * @return mixed La commande mise à jour.
     */
    public function checkValueAndUpdateCmd($refStateId, $refStateValueArray, $timestamp) {
        $cmd = array();
        if (is_object($cmdInfo = $this->getCmd('info', $refStateId))) {
            if ($cmdInfo->getUnite() == '°C') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['CELSIUS'];
                if (isset($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray])) {
                    return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                }
            } elseif ($cmdInfo->getUnite() == '°F') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['FAHRENHEIT'];
                if (isset($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray])) {
                    return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                }
            }
            if ($cmdInfo->getConfiguration('originalType') == 'Array') {
                $langPack = $this->getLangJson('langPackProductType', '', '0.0');
                if (is_array($refStateValueArray)) {
                    $monitoring = $this->getConfiguration('Monitoring', '');

                    foreach ($refStateValueArray as $item) {
                        foreach ($item as $arrKey => $arrVal) {
                            $logicalid = array_search($arrKey, $monitoring);
                            if ($logicalid !== false) {
                                $cmd[$arrKey] = $this->getCmd('info', $logicalid);
                                if (is_object($cmd[$arrKey])) {
                             //log::add(__CLASS__, 'debug', 'TERMMMMMMM & => ' .$logicalid . ' => ' . $arrKey . ' ==== '  . json_encode($langPack[$arrVal]));
                                    $unTranslatedVal = $cmd[$arrKey]->getConfiguration('valueMapping')[$arrVal];
                                    if (is_array($langPack) && isset($unTranslatedVal) && (strpos($unTranslatedVal, '@') === 0)) {
                                        $arrVal = $langPack[$unTranslatedVal];
                                    }
                                    $cmd[$arrKey]->event($arrVal);
                                }
                            }
                        }
                    }
                    $refStateValueArray = json_encode($refStateValueArray);
                }
            }
        }
        return $this->checkAndUpdateCmd($refStateId, $refStateValueArray, $timestamp);
    }

    /**
     * Vérifie si l'équipement est connecté.
     *
     * Cette fonction vérifie si l'équipement est connecté en interrogeant l'état de la commande 'online'.
     *
     * @return bool True si l'équipement est connecté ; sinon, false.
     */
    public function isConnected() {
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

    /**
     * Crée une commande pour l'équipement.
     *
     * Cette fonction crée une nouvelle commande pour l'équipement en fonction des propriétés fournies.
     *
     * @param array $_properties Les propriétés de la commande à créer.
     * @param lgthinq2Cmd|null $_cmdInfo Les informations sur la commande, facultatif.
     * @return lgthinq2Cmd|null La commande créée si la création réussit ; sinon, retourne null.
     */
    public function createCommand($_properties, $_cmdInfo = null) {
        if ($this->getIsEnable()) {
            $type = (!isset($_properties['type'])?(!$_cmdInfo?'info':'action'):$_properties['type']);
            $cmd = $this->getCmd($type, $_properties['logicalId']);
            foreach ($this->getCmd() as $aCmd) {
                if ($aCmd->getName() == $_properties['name'] && $aCmd->getLogicalId() != $_properties['logicalId']) {
                    $_properties['name'] .= config::genKey(2);
                }
            }
            if (!is_object($cmd)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . __(' DEBUGGGG $_properties ', __FILE__) . is_object($cmd) . ' => ' . $_properties['logicalId']);
                $cmd = new lgthinq2Cmd();
                $cmd->setType($type);
                $cmd->setEqLogic_id($this->getId());
                utils::a2o($cmd, $_properties);
                $cmd->save();
            }
            return $cmd;
        }
    }

    /**
     * Renvoie le lien de l'image de l'équipement.
     *
     * Cette fonction renvoie le lien de l'image de l'équipement.
     *
     * @return string L'URL de l'image de l'équipement.
     */
    public function getImage() {
        $file = 'plugins/lgthinq2/core/config/img/' . $this->getConfiguration('deviceType') . '.png';
        if (is_file($file)) {
            return $file;
        } else {
            return 'plugins/lgthinq2/plugin_info/config/img/' . $this->getConfiguration('thumbnail', '../../../plugin_info/lgthinq2_icon.png');
        }
    }

    /**
     * Récupère une commande à partir de son ID logique.
     *
     * Cette fonction récupère les informations sur la commande correspondant à l'ID logique spécifié.
     *
     * @param string $_logicalId L'ID logique de la commande.
     * @return lgthinq2Cmd|false Les informations sur la commande si elles existent ; sinon, retourne false.
     */
    public function getCmdInfo($_logicalId = '') {
        if ($_logicalId == '') {
            return false;
        }
        if (is_object($cmd = $this->getCmd('info', $_logicalId))) {
            return $cmd;
        }
        return false;
    }

    /**
     * Génère le code HTML pour l'affichage de l'équipement.
     *
     * Cette fonction génère le code HTML pour l'affichage de l'équipement selon la version spécifiée.
     *
     * @param string $_version La version de l'affichage (par défaut : 'dashboard').
     * @return string Le code HTML généré pour l'affichage de l'équipement.
     */
    public function toHtml($_version = 'dashboard') {
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

    public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();
        log::add('lgthinq2', 'debug', __("Action sur ", __FILE__) . $this->getLogicalId() . __(" avec options ", __FILE__) . json_encode($_options));

        if ($this->getLogicalId() == 'refresh') {
            $eqLogic->refresh();
            return;
        }
        $resValue = '';

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
        $value = str_replace(array_keys($replace),$replace,$this->getConfiguration('updateLGCmdToValue', ''));
        $keyValue = str_replace(array_keys($replace),$replace,$this->getConfiguration('value', ''));
        if (lgthinq2::isValidJson($keyValue)) {
            $keyValue = json_decode($keyValue, true);
        }

        lgthinq2::getTokenIsExpired();

        if ($eqLogic->getConfiguration('platformType') == 'thinq1') {
            log::add('lgthinq2', 'debug', __("Données à envoyer en thinq1 ", __FILE__));

            $headers = lgthinq2::defaultDevicesEmpHeaders();
            $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', 'lgthinq2', lgthinq2::step6());
            log::add('lgthinq2', 'debug', __("Données à envoyer en thinq1 headers ", __FILE__) . json_encode($headers));

            $data = array(
                lgthinq2::DATA_ROOT => array(
                    'cmd' => $this->getConfiguration('cmd'),
                    'cmdOpt' => $this->getConfiguration('cmdOpt'),
                    'deviceId' => $eqLogic->getLogicalId(),
                    'value' => ($keyValue!=''?$keyValue:$value),
                    'workId' => lgthinq2::setUUID(),
                    'data' => ''
                )
            );

            $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiControl', json_encode($data, JSON_PRETTY_PRINT), $headers);
            if ($response) {
                $arr = json_decode($response, true);
                if (!$arr || !isset($arr[lgthinq2::DATA_ROOT])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($arr));
                    return;
                }
                if (!isset($arr[lgthinq2::DATA_ROOT]['returnCd'])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Erreur de la réponse ', __FILE__) . json_encode($arr[lgthinq2::DATA_ROOT]));
                    return;
                }
                if ($arr[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Code retour erroné ', __FILE__) . json_encode($arr[lgthinq2::DATA_ROOT]));
                    return;
                }
                if (isset($arr[lgthinq2::DATA_ROOT]['returnData'])) {
                    if ($arr[lgthinq2::DATA_ROOT]['format'] == 'B64') { // decode only base64
                        $resValue = json_decode(base64_decode($arr[lgthinq2::DATA_ROOT]['returnData']), true);
                    } else {
                        $resValue = $arr[lgthinq2::DATA_ROOT]['returnData']; // else put it on string info cmd
                    }
                    if (is_array($resValue)) {
                        $resValue = json_encode($resValue); // to send on string infos cmd
                        foreach ($resValue as $resValueK => $resValueV) { // to update each key if cmd exists also
                            $cmdResV = $this->getCmd('info', $resValueV);
                            if (is_object($cmdResV)) {
                                $cmdResV->event($resValueV);
                            }
                        }
                    }
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' : Réponse décodée récupérée ' . $resValue);
                }
            }

        } elseif ($eqLogic->getConfiguration('platformType') == 'thinq2') {
            log::add('lgthinq2', 'debug', __("Données à envoyer en thinq2 ", __FILE__));

            $headers = lgthinq2::defaultDevicesHeaders();

            $data = array(
                'command' => $this->getConfiguration('cmd'),
                'ctrlKey' => $this->getConfiguration('ctrlKey'),
                //'dataSetList' => array(),
                'dataKey' => $this->getConfiguration('dataKey', null),
                'dataValue' => $this->getConfiguration('dataValue', $value)
            );
            $refState = lgthinq2::deviceTypeConstantsState($eqLogic->getConfiguration('deviceType')); // to get "resState" keytree
            if ($refState && $value != '') {
                $data['dataSetList'] = array(
                    $refState => array(
                       str_replace($this->getConfiguration('cmd'), '', $this->getLogicalId()) => $value
                    )
                );
            }

            log::add('lgthinq2', 'debug', __("Donnée envoyée en thinq2 ", __FILE__) . json_encode($data));
            $response = lgthinq2::postData(lgthinq2::LGTHINQ2_SERV_DEVICES . $eqLogic->getLogicalId() . '/control-sync', json_encode($data, JSON_NUMERIC_CHECK), $headers);
            log::add('lgthinq2', 'debug', __("Répopnse reçue en thinq2 ", __FILE__) . $response);
            if ($response) {
                $arr = json_decode($response, true);
                if (!$arr || !isset($arr['resultCode'])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Erreur de la requête ', __FILE__) . json_encode($arr));
                    return;
                }
                if ($arr['resultCode'] != '0000') {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Erreur de code ', __FILE__) . json_encode($arr));
                    return;
                }
                if (is_array($arr['result'])) {
                    if (isset($arr['result']['data']) && $arr['result']['data']['value']) { // to put plain result on string info cmd
                        $resValue = $arr['result']['data']['value'];
                    } else {
                        $resValue = json_encode($arr['result']); // else put result as json in string info cmd
                    }
                } else {
                    $resValue = $arr['result']; // not an array = plain text into string info cmd
                }
            }
        }

        if ($this->getConfiguration('updateLGCmdId') != '') { // get action cmd config message and put result in cmd info result
            $cmd = cmd::byId($this->getConfiguration('updateLGCmdId'));
            if (is_object($cmd)) {
                $value = $this->getConfiguration('updateLGCmdToValue');
                switch ($this->getSubType()) {
                    case 'slider':
                        $value = str_replace('#slider#', $options['slider'], $value);
                        break;
                    case 'color':
                        $value = str_replace('#color#', $options['color'], $value);
                        break;
                    case 'select':
                        $value = str_replace('#select#', $options['select'], $value);
                        break;
                    case 'message':
                        $value = str_replace('#message#', $options['message'], $value);
                        break;
                    case 'other':
                        $value = $resValue;
                        break;
                }
                log::add('lgthinq2', 'debug', __FUNCTION__ . __(' Réponse décodée ', __FILE__) . $resValue . __(' transmise dans ', __FILE__) . $cmd->getName());
                $cmd->event($value);
            }
        }
        return true;
    }
}
