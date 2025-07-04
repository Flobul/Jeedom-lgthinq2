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
require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../../../plugins/lgthinq2/core/class/lgthinq2.customLang.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class lgthinq2 extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '1.04';
    public static $_widgetPossibility   = array('custom' => true, 'custom::layout' => true);

    const LGTHINQ_GATEWAY       = 'https://route.lgthinq.com:46030/v1/service/application/gateway-uri';
    const LGTHINQ_GATEWAY_LIST  = 'https://kic.lgthinq.com:46030/api/common/gatewayUriList';
    const LGAPI_DATETIME        = 'https://fr.lgeapi.com/datetime';
    const LGTHINQ1_SERV_DEVICES = 'https://eic.lgthinq.com:46030/api/';
    const LGTHINQ2_SERV_URL     = 'https://eic-service.lgthinq.com:46030/v1/';

    const LGTHINQ_MQTT_URL      = 'https://common.lgthinq.com/route';
    const LGTHINQ_MQTT_CER      = 'https://www.amazontrust.com/repository/AmazonRootCA1.pem';
    const LGTHINQ_MQTT_AZU      = 'https://lgthinq.azurewebsites.net/api/certdata';

    const APPLICATION_KEY       = '6V1V8H2BN5P9ZQGOI5DAQ92YZBDO3EK9';
    const OAUTHSECRETKEY        = 'c053c2a6ddeb7ad97cb0eed0dcb31cf8';
    const APPKEY                = 'LGAO221A02';
    const SVCCODE               = 'SVC202';
    const XAPIKEY               = 'VGhpblEyLjAgU0VSVklDRQ=='; //base64 "ThinQ2.0 SERVICE"
    const DATA_ROOT             = 'lgedmRoot';
    const MAXRETRY              = 3;

    const USER_AGENT            = 'LG ThinQ/5.0.25141 (iPhone; iOS 17.5.1; Scale/3.00)';
    const X_OS_VERSION          = '17.5.1';
    const X_APP_VERSION         = '5.0.25141';
    const X_THINQ_APP_VER       = '5.0.2400';
    const X_SERVICE_PHASE       = 'OP';
    const X_THINQ_APP_LOGINTYPE = 'LGE';
    const X_THINQ_APP_LEVEL     = 'PRD';
    const X_THINQ_APP_OS        = 'IOS';
    const X_THINQ_APP_TYPE      = 'NUTS';

    /**
     * Renvoie le libellé correspondant à un type de périphérique à partir de son identifiant.
     *
     * Cette méthode prend en paramètre l'identifiant numérique d'un type de périphérique et renvoie son libellé associé.
     * Si aucun libellé correspondant n'est trouvé pour l'identifiant donné, l'identifiant lui-même est renvoyé.
     *
     * @param int $_id L'identifiant numérique du type de périphérique.
     * @return string Le libellé correspondant au type de périphérique ou l'identifiant lui-même s'il n'existe pas de libellé correspondant.
     */
    public static function deviceTypeConstants($_id)
    {
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
            406 => __('Chauffe-eau', __FILE__),
            407 => __('Ventilation', __FILE__),
            501 => __('Aspirateur robot', __FILE__),
            504 => __('Aspirateur balai', __FILE__),
            603 => __('Passerelle Cloud', __FILE__),
            604 => __('Chaise de massage', __FILE__),
            605 => __('Contrôleur filaire de luxe', __FILE__),
            701 => __('EnerVu', __FILE__),
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
            5000 => __('Éclairage', __FILE__),
            5001 => __('Éclairage', __FILE__),
            5002 => __('Éclairage', __FILE__),
            5003 => __('Éclairage', __FILE__),
            5100 => __('Raccordement', __FILE__),
            5101 => __('Raccordement', __FILE__),
            5102 => __('Pompe', __FILE__),
            5205 => __('Interrupteur', __FILE__),
            5300 => __('Capteur de contact', __FILE__),
            5301 => __('Capteur de lumière', __FILE__),
            5302 => __('Capteur d\'occupation', __FILE__),
            5303 => __('Capteur de température', __FILE__),
            5304 => __('Capteur de pression', __FILE__),
            5305 => __('Capteur de débit', __FILE__),
            5307 => __('Capteur d\'humidité', __FILE__),
            5400 => __('Verrouillage de porte', __FILE__),
            5402 => __('Couverture de fenêtre', __FILE__),
            5500 => __('Unité de chauffage / refroidissement', __FILE__),
            5501 => __('Thermostat', __FILE__),
            5502 => __('Capteur de lumière', __FILE__),
            10000 => __('Télévision', __FILE__),
            10101 => __('Hub HEJ', __FILE__),
            20000 => __('Montre', __FILE__),
            30101 => __('Gram', __FILE__),
            30102 => __('UltraPC', __FILE__),
            90000 => __('Autre modèle', __FILE__)
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:$_id;
    }

    public static function deviceTypeCategory($_id)
    {
        $_deviceTypes = array(
            101 => 'Fridge',
            102 => 'Fridge',
            103 => 'Water',
            201 => 'Laundry',
            202 => 'Dryer',
            203 => 'Styler',
            204 => 'Dishwasher',
            221 => 'Laundry',
            222 => 'Dryer',
            301 => 'Oven',
            302 => 'Range',
            303 => 'Cooktop',
            304 => 'Hood',
            401 => 'AirCon',
            402 => 'AirPurifier',
            403 => 'Dehumidifier',
            501 => 'Homebot',
            604 => 'Mchair',
            3003 => 'Light'
        );
        return isset($_deviceTypes[$_id])?$_deviceTypes[$_id]:null;
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
    public static function deviceTypeCodeConstants($_id)
    {
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
    public static function deviceTypeConstantsIcon($_id)
    {
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
    public static function deviceTypeConstantsState($_id)
    {
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
     * Vérifie la santé des connexions et des droits d'accès.
     *
     * @return array Résultat des tests de santé.
     */
    public static function health()
    {
        $return = array();
        $cron = cron::byClassAndFunction(__CLASS__, 'update');
        $running = false;
        if (is_object($cron)) {
            $running = $cron->getEnable(0);
        }
        $return[] = array(
            'test' => __('Tâche de synchronisation', __FILE__),
            'result' => (($running) ? __('OK', __FILE__) : __('NOK', __FILE__)) . ' (' . $cron->getCache('runtime') . 's)',
            'advice' => ($running) ? '' : __('Allez sur la page du moteur des tâches et vérifiez lancer la tache lgthinq2::update', __FILE__),
            'state' => $running
        );
        $token = config::byKey('token', __CLASS__);
        $expires = config::byKey('expires_in', __CLASS__);
        $calcExpiracy = ($expires - time());
        $isExpired = ($calcExpiracy < 0 ? __('expiré depuis ', __FILE__) : __('expire dans ', __FILE__)) . abs($calcExpiracy) . ' ' . __('secondes', __FILE__);
        $return[] = array(
            'test' => __('Jeton d\'accès', __FILE__),
            'result' => ($calcExpiracy > 0 ? __('OK', __FILE__) : __('NOK', __FILE__)) . ' : ' . $isExpired,
            'advice' => ($calcExpiracy > 0 ? '' : __('Choisissez un intervalle de rafraîchissement, ou redémarrez le démon. Cela va mettre à jour le jeton', __FILE__)),
            'state' => ($calcExpiracy > 0)
        );
        return $return;
    }

    /**
     * Vérifie si une chaîne de caractères est un JSON valide.
     *
     * Cette fonction vérifie si une chaîne de caractères est un JSON valide.
     *
     * @param string $string La chaîne de caractères à vérifier.
     * @return bool True si la chaîne est un JSON valide ; sinon, false.
     */
    public static function isValidJson($string)
    {
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
    public static function replaceBeginString($_needle, $_haystack)
    {
        if (substr($_haystack, 0, strlen($_needle)) == $_needle) {
            return substr($_haystack, strlen($_needle));
        }
        return false;
    }

    /**
     * Effectue une requête GET vers une URL donnée avec les données et les en-têtes spécifiés.
     *
     * Cette méthode prend en paramètres l'URL cible, les données à envoyer et les en-têtes HTTP à inclure dans la requête.
     * Elle effectue une requête GET vers l'URL avec les paramètres spécifiés et renvoie la réponse obtenue.
     *
     * @param string $url L'URL cible de la requête GET.
     * @param array|string $data Les données à envoyer dans la requête GET, sous forme de tableau associatif ou de chaîne de requête.
     * @param array $headers Les en-têtes HTTP à inclure dans la requête GET, sous forme de tableau.
     * @return string|false La réponse de la requête GET, ou false en cas d'échec.
     */
    public static function getData($url, $headers)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($curl);
        if ($response === false) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de requête : ', __FILE__) . curl_error($curl));
        }
        curl_close($curl);
        return $response;
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
    public static function postData($url, $data, $headers)
    {
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
        if ($response === false) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de requête : ', __FILE__) . curl_error($curl));
        }
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
    public static function doRetry($stepFunction, $rawContent = false)
    {
        $result = null;
        for ($i = 1; $i <= lgthinq2::MAXRETRY; $i++) {
            $result = $stepFunction();
            if (!$result) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape a échoué, tentative ', __FILE__) . $i . '/' . lgthinq2::MAXRETRY);
            } else {
                if ($rawContent) {
                    $res = json_decode($result, true);
                    if ($res && isset($res['error']) && isset($res['error']['message'])) {
                        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape a échoué ', __FILE__) . $res['error']['message'] . ', tentative ' . $i . '/' . lgthinq2::MAXRETRY);
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
    public static function getClientId()
    {
        if (config::byKey('cliend_id', __CLASS__, '') == '') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Création du client_id ', __FILE__));
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
    public static function getLanguage($_type)
    {
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
    public static function oldDefaultHeaders()
    {
        return array(
            'Accept: */*',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'origin: ' . config::byKey('LGE_MEMBERS_URL', __CLASS__),
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'User-Agent: ' . lgthinq2::USER_AGENT,
            'X-Requested-With: XMLHttpRequest'
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les services LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les services LG ThinQ.
     */
    public static function defaultHeaders()
    {
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
    public static function defaultGwHeaders()
    {
        return array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ';q=1',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: ' . lgthinq2::USER_AGENT,
            'x-api-key: ' . lgthinq2::XAPIKEY,
            'x-app-version: ' . lgthinq2::X_APP_VERSION,
            'x-client-id: ' . lgthinq2::getClientId(),
            'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
            'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
            'x-message-id: ' . bin2hex(random_bytes(22)),
            'x-model-name: iPhone SE(2nd Gen)',
            'x-origin: app-native',
            'x-os-version: ' . lgthinq2::X_OS_VERSION,
            'x-service-code: ' . lgthinq2::SVCCODE,
            'x-service-phase: ' . lgthinq2::X_SERVICE_PHASE,
            'x-thinq-app-logintype: ' . lgthinq2::X_THINQ_APP_LOGINTYPE,
            'x-thinq-app-level: ' . lgthinq2::X_THINQ_APP_LEVEL,
            'x-thinq-app-os: ' . lgthinq2::X_THINQ_APP_OS,
            'x-thinq-app-type: ' . lgthinq2::X_THINQ_APP_TYPE,
            'x-thinq-app-ver: ' . lgthinq2::X_THINQ_APP_VER,
            'x-user-no: ' . config::byKey('user_number', __CLASS__)
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ.
     */
    public static function defaultDevicesHeaders()
    {
        return array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ' . lgthinq2::getLanguage('hyphen') . ';q=1.0',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: ' . lgthinq2::USER_AGENT,
            'x-api-key: ' . lgthinq2::XAPIKEY,
            'x-app-version: ' . lgthinq2::X_APP_VERSION,
            'x-client-id: ' . lgthinq2::getClientId(),
            'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
            'x-emp-token: ' . config::byKey('access_token', __CLASS__),
            'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
            'x-message-id: ' . bin2hex(random_bytes(22)),
            'x-model-name: iPhone SE(2nd Gen)',
            'x-origin: app-native',
            'x-os-version: ' . lgthinq2::X_OS_VERSION,
            'x-service-code: ' . lgthinq2::SVCCODE,
            'x-service-phase: ' . lgthinq2::X_SERVICE_PHASE,
            'x-thinq-app-logintype: ' . lgthinq2::X_THINQ_APP_LOGINTYPE,
            'x-thinq-app-level: ' . lgthinq2::X_THINQ_APP_LEVEL,
            'x-thinq-app-os: ' . lgthinq2::X_THINQ_APP_OS,
            'x-thinq-app-type: ' . lgthinq2::X_THINQ_APP_TYPE,
            'x-thinq-app-ver: ' . lgthinq2::X_THINQ_APP_VER,
            'x-user-no: ' . config::byKey('user_number', __CLASS__)
        );
    }

    /**
     * Renvoie les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ avec EMP.
     *
     * @return array Les en-têtes par défaut pour les requêtes vers les appareils LG ThinQ avec EMP.
     */
    public static function defaultDevicesEmpHeaders()
    {
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
    public static function getPassword($_encrypted = false)
    {
        return $_encrypted ? hash('sha512', config::byKey('password', __CLASS__)) : config::byKey('password', __CLASS__);
    }

    /**
     * Renvoie le nom d'utilisateur configuré, éventuellement encodé pour une utilisation dans une URL.
     *
     * @param bool $_urlEncoded Indique si le nom d'utilisateur doit être encodé pour une utilisation dans une URL.
     * @return string Le nom d'utilisateur configuré.
     */
    public static function getUsername($_urlEncoded = false)
    {
        return $_urlEncoded ? urlencode(config::byKey('id', __CLASS__)) : config::byKey('id', __CLASS__);
    }

    /**
     * Vérifie si une chaîne est au format JSON valide.
     *
     * Cette fonction prend en entrée une chaîne de caractères ($string) et tente de la décoder en JSON à l'aide de la fonction json_decode().
     * Elle retourne true si la chaîne est au format JSON valide (c'est-à-dire si aucune erreur n'est survenue lors du décodage), sinon elle retourne false.
     *
     * @param string $string La chaîne de caractères à vérifier.
     * @return bool Retourne true si la chaîne est au format JSON valide, sinon false.
     */
    public static function isJson($string) {
       json_decode($string);
       return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Nettoie une chaîne de caractères pour la rendre conforme au format JSON.
     *
     * Cette fonction prend en entrée une chaîne de caractères ($string) et effectue plusieurs opérations pour la nettoyer et la rendre conforme au format JSON.
     * Elle supprime d'abord les caractères d'ordre de marque (BOM) Unicode s'ils sont présents au début de la chaîne.
     * Ensuite, elle décode les entités HTML spéciales en caractères UTF-8 et supprime les espaces vides au début et à la fin de la chaîne.
     * La chaîne nettoyée est ensuite retournée en sortie de la fonction.
     *
     * @param string $string La chaîne de caractères à nettoyer.
     * @return string La chaîne nettoyée conforme au format JSON.
     */
    public static function cleanJson($string) {
        if (substr($string, 0, 3) == "\xef\xbb\xbf") {
            $string = substr($string, 3);
        }
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    /**
     * Étape 0 : Obtient les informations de la passerelle LG ThinQ.
     *
     * @return bool|string Retourne true en cas de succès, sinon retourne null.
     */
    public static function step0()
    {
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
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 0 a planté ', __FILE__) . json_encode($gatewayRes));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 0 ', __FILE__) . json_encode($gatewayRes));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 0 a result ', __FILE__) . parse_url($gatewayRes['result']['uris']['empFrontBaseUri2'], PHP_URL_HOST));

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
    public static function step1()
    {
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
    public static function oldStep2($rep1)
    {
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
    public static function step2($rep1)
    {
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
    public static function step3($accountData)
    {
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
    public static function step4()
    {
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
    public static function step5($code, $time)
    {
        $headers = array(
            'content-type: application/x-www-form-urlencoded',
            'accept: application/json',
            'accept-language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
            'x-lge-appkey: ' . lgthinq2::APPKEY,
            'x-lge-app-os: IOS',
            'accept-encoding: gzip, deflate, br',
            'x-model-name: Apple/iPhone SE(2nd Gen)',
            'user-agent: LG%20ThinQ/54 CFNetwork/1410.0.3 Darwin/22.6.0',
            'x-app-version: ' . lgthinq2::X_APP_VERSION,
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
    public static function step6()
    {
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
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 6 a échoué.', __FILE__));
            return;
        }
        $arr6 = json_decode($response, true);
        if (!$arr6 || !isset($arr6[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($arr6));
            return;
        }
        if (!isset($arr6[lgthinq2::DATA_ROOT]['returnCd'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la réponse ', __FILE__) . json_encode($arr6[lgthinq2::DATA_ROOT]));
            return;
        }
        if ($arr6[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Code retour erroné ', __FILE__) . json_encode($arr6[lgthinq2::DATA_ROOT]));
            return;
        }
        return $arr6[lgthinq2::DATA_ROOT]['jsessionId'];
    }

    /**
     * Méthode de connexion.
     */
    public static function login()
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('debut', __FILE__));

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __(' : ÉTAPE 0', __FILE__));
        $rep0 = lgthinq2::step0();

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __(' : ÉTAPE 1', __FILE__));
        $rep1 = lgthinq2::doRetry('lgthinq2::step1');
        if (!$rep1) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 1 a échoué après plusieurs tentatives.', __FILE__));
            return;
        }
        $spxLogin = json_decode($rep1, true);
        if (!$spxLogin || !isset($spxLogin['encrypted_pw'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 2 a planté ', __FILE__) . json_encode($spxLogin));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : EncryptedPw = ' . $rep1);

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __(' : ÉTAPE 2', __FILE__));
        $rep2 = lgthinq2::doRetry(function() use ($spxLogin) { return lgthinq2::step2($spxLogin); }, true);
        if (!$rep2) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 2 a échoué après plusieurs tentatives.', __FILE__));
            return;
        }
        $accountData = json_decode($rep2, true);
        if (!$accountData || !isset($accountData['account'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 2 a planté', __FILE__) . json_encode($accountData));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ACCOUNT INFOS = ' . json_encode($accountData['account']));
        config::save('loginSessionID', $accountData['account']['loginSessionID'], __CLASS__);
        $timeToExp = explode(';', $accountData['account']['loginSessionID'])[1];
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : START TIME = ' . $timeToExp);

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __(' : ÉTAPE 3', __FILE__));
        $rep3 = lgthinq2::doRetry(function() use ($accountData) { return lgthinq2::step3($accountData); });
        if (!$rep3) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 3 a échoué après plusieurs tentatives.', __FILE__));
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
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Aucun paramètre d\'URL trouvé dans la clé redirect_uri.', __FILE__));
            return;
        }

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('ÉTAPE 4', __FILE__));
        $rep4 = lgthinq2::step4();
        if (!$rep4) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 4 a échoué.', __FILE__));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : REPTIME = ' . $rep4);
        $time = json_decode($rep4, true);
        if (!$time || !isset($time['date'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Impossible de récupérer l\'heure.', __FILE__));
            return;
        }
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $rfc2822Date = ($time['date']?$time['date']:$dateTime->format(DateTime::RFC2822));

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('ÉTAPE 5', __FILE__));
        $rep5 = lgthinq2::step5($code, $time);
        if (!$rep5) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Étape 5 a échoué.', __FILE__));
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' : ACCESS/REFRESH TOKENS = ' . $rep5);
        $token = json_decode($rep5, true);
        if (!$token || !isset($token['access_token'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Impossible de récupérer le token d\'accès.', __FILE__));
            return;
        }

        config::save('access_token', $token['access_token'], __CLASS__);
        config::save('expires_in', (intval($timeToExp/1000) + $token['expires_in']), __CLASS__);
        config::save('refresh_token', $token['refresh_token'], __CLASS__);
        config::save('oauth2_backend_url', $token['oauth2_backend_url'], __CLASS__);

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('ÉTAPE 6', __FILE__));
        $jsession = lgthinq2::step6();
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Jeton de session ', __FILE__) . $jsession);

        config::save('jsessionId', $jsession, __CLASS__);
    }

    public static function terms()
    {
        try {
            $showTermUrl = "/common/showTerms?callback_url=lgaccount.lgsmartthinq:/updateTerms&country=FR&language=fr-FR&division=ha:T20&terms_display_type=3&svc_list=SVC202";
            $empSpxUri = config::byKey('LGACC_SPX_URL', __CLASS__);
            $empTermsUri = config::byKey('LG_EMPTERMS_URL', __CLASS__);

            $accessToken = config::byKey('access_token', __CLASS__);

            // Étape 1 : Obtenir le HTML des termes à partir de l'URL.
            $headers = array(
                'X-Login-Session: ' . $accessToken,
            );

            $showTermHtml = lgthinq2::postData($empSpxUri . '/' . $showTermUrl, '', $headers);
            if (!$showTermHtml) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Échec lors de la récupération du HTML des termes.', __FILE__));
                return false;
            }

            // Extraction de la signature et du timestamp.
            if (preg_match('/signature\s+:\s+"([^"]+)"/', $showTermHtml, $signatureMatch) &&
                preg_match('/tStamp\s+:\s+"([^"]+)"/', $showTermHtml, $tStampMatch)) {
                $signature = $signatureMatch[1];
                $tStamp = $tStampMatch[1];
            } else {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Impossible d\'extraire la signature ou le timestamp.', __FILE__));
                return false;
            }

            // Configuration des headers pour les requêtes suivantes.
            $headers = array(
                'referer: https://fr.m.lgaccount.com/',
                'x-login-session: ' . $accessToken,
                'x-timestamp: ' . $tStamp,
                'x-device-language: ' . str_replace('_', '-', config::byKey('language', __CLASS__, 'fr_FR')),
                'x-device-type: M01',
                'x-device-publish-flag: Y',
                'origin: https://fr.m.lgaccount.com',
                'x-device-country: ' . lgthinq2::getLanguage('uppercase'),
                'x-signature: ' . $signature,
                'x-device-platform: ADR',
                'x-application-key: ' . lgthinq2::APPLICATION_KEY,
                'x-lge-svccode: SVC709',
                'accept-language: '.str_replace('_', '-', config::byKey('language', __CLASS__, 'fr_FR')).','.lgthinq2::getLanguage('lowercase').';q=0.9',
                'accept: application/json',
                'content-type: application/x-www-form-urlencoded;charset=UTF-8',
                'access-control-allow-origin: *',
                'x-device-language-type: IETF',
                'accept-encoding: gzip, deflate, br'
                //'X-Client-App-Key: ' . lgthinq2::APPKEY,
            );

            // Étape 2 : Obtenir les termes du compte.
            $accountTermUrl = "/emp/v2.0/account/user/terms?opt_term_cond=001&term_data=SVC202&itg_terms_use_flag=Y&dummy_terms_use_flag=Y";
            $accountTermsResponse = lgthinq2::getData($empTermsUri . $accountTermUrl, $headers);
            if (!$accountTermsResponse) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Échec lors de la récupération des termes du compte.', __FILE__));
                return false;
            }
            $accountTerms = json_decode($accountTermsResponse, true)['account']['terms'];

            // Étape 3 : Obtenir les informations sur les termes.
            $termInfoUrl = "/emp/v2.0/info/terms?opt_term_cond=001&only_service_terms_flag=&itg_terms_use_flag=Y&term_data=SVC202";
            $infoTermsResponse = lgthinq2::getData($empTermsUri . $termInfoUrl, $headers);
            if (!$infoTermsResponse) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Échec lors de la récupération des informations sur les termes.', __FILE__));
                return false;
            }
            $infoTerms = json_decode($infoTermsResponse, true)['info']['terms'];

            // Étape 4 : Vérifier les termes qui nécessitent un nouvel accord.
            $newTermAgreeNeeded = array_filter($infoTerms, function($term) use ($accountTerms) {
                return !in_array($term['termsID'], $accountTerms);
            });

            // Étape 5 : Si des termes nécessitent un accord, les accepter.
            if (!empty($newTermAgreeNeeded)) {
                $newTermsData = array(
                    'terms' => implode(',', array_map(function ($term) {
                        return $term['termsType'] . ':' . $term['termsID'] . ':' . $term['defaultLang'];
                    }, $newTermAgreeNeeded))
                );

                $acceptTermsUrl = '/emp/v2.0/account/user/terms';

                $acceptTermsResponse = lgthinq2::postData($empTermsUri . $acceptTermsUrl, http_build_query($newTermsData), $headers);
                //$acceptTermsUrl = "/lgacc/front/v1/common/insertUserTerms";
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Nouveaux termes $newTermsData.', __FILE__) .  http_build_query($newTermsData));
                //$acceptTermsResponse = lgthinq2::postData(config::byKey('LGE_MEMBERS_URL', __CLASS__) . $acceptTermsUrl, http_build_query($newTermsData), $headers);
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Nouveaux termes $acceptTermsResponse.', __FILE__) . $acceptTermsResponse);
                if (!$acceptTermsResponse) {
                    log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Échec lors de l\'acceptation des nouveaux termes.', __FILE__));
                    return false;
                }
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Nouveaux termes acceptés.', __FILE__));
            }

            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Tous les termes sont à jour.', __FILE__));
            return true;
        } catch (Exception $e) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur : ', __FILE__) . $e->getMessage());
            return false;
        }
    }

    /**
     * Renvoie les informations sur le démon lgthinq2d.
     *
     * @return array Tableau associatif contenant les informations sur le démon (log, state, launchable)
     */
    public static function deamon_info()
    {
        $return = array();
        $return['log'] = __CLASS__;
        $return['state'] = 'nok';
        $pid = trim(shell_exec('ps ax | grep "/lgthinq2d.php" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0') {
            $return['state'] = 'ok';
        }
        $return['launchable'] = 'ok';
        if (PHP_VERSION_ID < 70400) {
            $return['state'] = 'nok';
            $return['launchable'] = 'nok';
        }
        return $return;
    }

    /**
     * Lance le service lgthinq2d
     *
     * @param bool $_debug Active le mode débogage
     * @throws Exception si la configuration est incorrecte
     * @return bool Vrai si le démon a été lancé, faux sinon
     */
    public static function deamon_start($_debug = false)
    {
        log::add(__CLASS__, 'info', __('Lancement du service lgthinq2', __FILE__));
        $deamon_info = self::deamon_info();
        if (PHP_VERSION_ID < 70400) {
            return false;
        }
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        if ($deamon_info['state'] == 'ok') {
            self::deamon_stop();
            sleep(2);
        }
        log::add(__CLASS__, 'info', __('Lancement du démon lgthinq2', __FILE__));
        $cmd = substr(dirname(__FILE__),0,strpos (dirname(__FILE__),'/core/class')).'/resources/lgthinq2d.php';
        log::add(__CLASS__, 'debug', __('Commande du démon : ', __FILE__) . $cmd);
        $result = exec('sudo php ' . $cmd . ' >> ' . log::getPathToLog('lgthinq2') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
            log::add(__CLASS__, 'error', 'Deamon error : ' . $result);
            return false;
        }
        sleep(1);
        $i = 0;
        while ($i < 30) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 30) {
            log::add(__CLASS__, 'error', 'Impossible de lancer le démon lgthinq2d', 'unableStartDeamon');
            return false;
        }
        log::add(__CLASS__, 'info', __('Démon lgthinq2d lancé', __FILE__));
        return true;
    }

    /**
     * Arrête le service lgthinq2.
     *
     * @return bool Retourne vrai si le démon a été arrêté avec succès, faux sinon.
     * @throws Exception Lève une exception si la configuration est invalide.
     */
    public static function deamon_stop()
    {
        log::add(__CLASS__, 'info', __('Arrêt du service lgthinq2', __FILE__));
        $cmd = '/lgthinq2d.php';
        exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
        sleep(1);
        exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
        sleep(1);
        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok') {
            exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
            sleep(1);
        } else {
            return true;
        }
        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok') {
            exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
            sleep(1);
            return true;
        }
    }

    /**
     * Traduit les valeurs du mapping de valeurs en utilisant un tableau de traduction.
     *
     * Cette fonction parcourt le tableau de mapping de valeurs fourni ($_arrayVM) et remplace les valeurs
     * qui commencent par '@' par leur traduction correspondante, si elles existent dans le tableau de traduction ($_trans).
     *
     * @param array $_arrayVM Le tableau de mapping de valeurs à traduire.
     * @param array $_trans Le tableau de traduction contenant les correspondances clé-valeur pour la traduction.
     * @return array Le tableau de mapping de valeurs traduit.
     */
    public static function translateValueMapping($_arrayVM, $_trans)
    {
        foreach ($_arrayVM as $key => $value) {
            if (isset($value['label']) && (strpos($value['label'], '@') === 0)) {
                if (array_key_exists($value['label'], $_trans)) {
                    $label = $value['label'];
                    $_arrayVM[$key]['label'] = $_trans[$label];
                }
            } else if (!is_array($value) && $value != '' && (strpos($value, '@') === 0)) {
                if (array_key_exists($value, $_trans)) {
                    $label = $value;
                    $_arrayVM[$key] = $_trans[$label];
                }
            }
        }
        return $_arrayVM;
    }

    /**
     * Vérifie si le token est expiré.
     *
     * @return bool Retourne true si le token est expiré et a été rafraîchi avec succès, sinon retourne false.
     */
    public static function getTokenIsExpired()
    {
        if (config::byKey('expires_in', __CLASS__, 0) < time()) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('refresh_token en cours, expiré depuis ', __FILE__) . (time() - config::byKey('expires_in', __CLASS__, 0)) . __(' secondes', __FILE__));
            return lgthinq2::refreshToken();
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('refresh_token à jour, il expire dans ', __FILE__) . (config::byKey('expires_in', __CLASS__, 0) - time()) . __(' secondes', __FILE__));
        return false;
    }

    /**
     * Rafraîchit le token d'accès.
     *
     * @return string|void Retourne la réponse de la requête de rafraîchissement du token d'accès si réussie, sinon rien.
     */
    public static function refreshToken()
    {
        $refreshToken = config::byKey('refresh_token', __CLASS__, '');
        if ($refreshToken != '') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('refresh_token en cours...', __FILE__));
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
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('refresh_token résultat : ', __FILE__) . $rep);
            $token = json_decode($rep, true);
            if (!$token || !isset($token['access_token'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : Impossible de récupérer le token d\'accès.');
                return;
            }
            config::save('access_token', $token['access_token'], __CLASS__);
            config::save('expires_in', (time() + $token['expires_in']), __CLASS__);
            config::save('jsessionId', lgthinq2::step6(), __CLASS__); //because jessionId is related to current access_token, it needs to be asked again

            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('refresh_token effectué ', __FILE__));
            return $rep;
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Pas de refresh_token, demande de login', __FILE__));
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
    public static function getDevices($_deviceId = '', $_tokenRefreshed = false)
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('appareil ', __FILE__) . $_deviceId);
        lgthinq2::getTokenIsExpired();

        $curl = curl_init();
        $headers = lgthinq2::defaultDevicesHeaders();

        curl_setopt_array($curl, array(
            CURLOPT_URL => lgthinq2::LGTHINQ2_SERV_URL . 'service/devices/' /* . $_deviceId*/, // rollback: all langPacks are not sent on individual deviceId
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

        $response = curl_exec($curl);
        curl_close($curl);
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('getDEVICES : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' erreur : '. $response);
            return;
        }
        $devices = json_decode($response, true);
            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/MAG.json'),true); // developper only
        if (!$devices || !isset($devices['resultCode'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($devices['resultCode'] != '0000' && $_tokenRefreshed == false) {
            lgthinq2::getDevices($_deviceId, true);
        }

        if ($devices['result'] && is_array($devices['result'])) {
            $translation = new lgthinq2_customLang();
            $customLangFile = $translation->customlang;

            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/SKY.json'),true); // developper only
            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/ROM.json'),true); // developper only
            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/PAC.json'),true); // developper only
            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/POC.json'),true); // developper only
            //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/MAG.json'),true); // developper only

            if (!isset($devices['result']['item'])) return;
            foreach ($devices['result']['item'] as $items) {
                if ($_deviceId != '' && $_deviceId != $items['deviceId']) continue;
                $eqLogic = lgthinq2::createEquipement($items, $items['platformType']);
                if (is_object($eqLogic) && isset($items['modelJsonUri'])) {
                    $refState = lgthinq2::deviceTypeConstantsState($eqLogic->getConfiguration('deviceType'));
                    $langProduct = $eqLogic->getLangJson('langPackProductType', $items['langPackProductTypeUri'], $items['langPackProductTypeVer']);
                    $langModel = $eqLogic->getLangJson('langPackModel', $items['langPackModelUri'], $items['langPackModelVer']);
                    //regroup translation array configModel and configProduct
                    if ($langProduct && is_array($langProduct) && $langModel && is_array($langModel)) {
                        $langPack = array_replace_recursive($langProduct, $langModel);
                    } else {
                        $langPack = $langProduct;
                    }
                    //regroup translation array configFile and langPackFile
                    $langPackCP = json_decode(file_get_contents(__DIR__ . '/../../data/langPack_CP.json'),true);
                    if ($langPack && is_array($langPack) && $langPackCP && is_array($langPackCP) && isset($langPackCP['pack'])) {
                        $langPack = array_replace_recursive($langPack, $langPackCP['pack']);
                    }
                    //regroup translation array configLangPackFile and customFile
                    if ($langPack && is_array($langPack) && $customLangFile && is_array($customLangFile)) {
                        $langPack = array_replace_recursive($langPack, $customLangFile);
                    }

                    if ($refState) {
                        $eqLogic->createCmdFromModelAndLangFiles($items['modelJsonUri'], $items['modelJsonVer'], $items['snapshot'][$refState], $langPack, $refState);
                    } else {
                        // cas où les infos sont directement sans dossier
                        $eqLogic->createCmdFromModelAndLangFiles($items['modelJsonUri'], $items['modelJsonVer'], $items['snapshot'], $langPack);
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
    public static function setUUID($data = null)
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Décode les données binaires d'un moniteur en utilisant un protocole spécifié.
     *
     * Cette fonction décode les données binaires fournies ($_undecodeddata) en utilisant un protocole spécifié ($_protocol).
     * Le protocole est défini comme un tableau associatif avec chaque élément contenant les informations nécessaires pour extraire une valeur spécifique des données binaires.
     * La fonction parcourt les données binaires et applique les règles du protocole pour extraire chaque valeur, puis retourne un tableau associatif contenant les valeurs extraites.
     *
     * @param string $_undecodeddata Les données binaires à décoder.
     * @param array $_protocol Le protocole de décodage spécifiant la structure des données binaires.
     * @return array Le tableau associatif contenant les valeurs extraites à partir des données binaires.
     */
    public static function decodeMonitorBinary($undecodeddata, $protocol) {
        $data = unpack('C*', base64_decode($undecodeddata), 1);
        $decoded = [];
        foreach ($protocol as $item) {
            $key = $item['value'];
            $value = 0;
            for ($i = $item['startByte']; $i < $item['startByte'] + $item['length']; $i++) {
                $v = $data[$i];
                $value = ($value << 8) + $v;
            }
            $decoded[$key] = strval($value);
        }
        return $decoded;
    }

    /**
     * Encode les données fournies en données binaires.
     *
     * Cette fonction encode les données fournies ($_data) en données binaires et retourne la représentation en base64 des données binaires encodées.
     * Les données fournies sont d'abord transformées en une liste d'octets (bytes) en utilisant la fonction pack de PHP, puis elles sont converties en une chaîne base64.
     *
     * @param array $_data Les données à encoder en données binaires.
     * @return string La représentation en base64 des données binaires encodées.
     */
    public static function encodeMonitorBinary($data) {
        return base64_encode(call_user_func_array('pack', array_merge(['c*'], $data)));
    }

    /**
     * Méthode appellée par le core (moteur de tâche) cron configuré dans la fonction lgthinq2_install
     * Lance une fonction pour récupérer les appareils et une fonction pour rafraichir les commandes
     */
    public static function update()
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__));
        $autorefresh = config::byKey('autorefresh', __CLASS__, 'never');
        if ($autorefresh != 'never') {
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
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('fin', __FILE__));
    }

    public static function cron5() {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__));
        foreach (eqLogic::byType(__CLASS__) as $eqLogic) {
            if ($eqLogic->getIsEnable()) {
                $deviceTypeCategory = lgthinq2::deviceTypeCategory($eqLogic->getConfiguration('deviceType'));
                if ($eqLogic->getConfiguration('platformType') == 'thinq1') {
                    $headers = lgthinq2::defaultDevicesEmpHeaders();
                    $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', __CLASS__, lgthinq2::step6());

                    $data = array(
                        lgthinq2::DATA_ROOT => array(
                            'deviceId' => $eqLogic->getLogicalId(),
                            'period' => 'Day_' . date('Ym01') . 'T000000Z/' .  date('Ymt') . 'T000000Z'
                        )
                    );
                    if ($eqLogic->getConfiguration('deviceType') == '101' || $eqLogic->getConfiguration('deviceType') == '102') {
                        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rms/inquiryWaterConsumptionInfo', json_encode($data), $headers);
                        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'energy/inquiryDoorInfoDay', json_encode($data), $headers);
                        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'energy/inquiryActiveSaving', json_encode($data), $headers);
                        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'energy/inquirySmartCareActiveSaving', json_encode($data), $headers);
                    }
                    if ($eqLogic->getConfiguration('deviceType') == '401') {
                        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . $deviceTypeCategory . '/inquiryPowerData', json_encode($data), $headers);
                    }
                } elseif ($eqLogic->getConfiguration('platformType') == 'thinq2') {
                    if ($eqLogic->getConfiguration('deviceType') == '101' || $eqLogic->getConfiguration('deviceType') == '102') {
                        $payload = array(
                            'period'    => 'day',
                            'startDate' => date('Y-m-01'),
                            'endDate'   => date('Y-m-t')
                        );
                        $energyHistory = $eqLogic->getEndpointQuery('energy-history', $payload, $deviceTypeCategory);
                        $eqLogic->checkCmdAndUpdateEnergyMonitor($energyHistory);

                        $doorOpenHistory = $eqLogic->getEndpointQuery('door-open-history', $payload, $deviceTypeCategory);
                        $eqLogic->checkCmdAndUpdateDoorOpenHistory($doorOpenHistory);

                        $payload3 = array_merge($payload, array('period' => 'month', 'lgTotalAverageInfo' => 'N', 'version' => 2));
                        $waterConsumption = $eqLogic->getEndpointQuery('water-consumption-history', $payload3, $deviceTypeCategory);
                        $eqLogic->checkCmdAndUpdateWaterConsumptionHistory($waterConsumption);

                        $frigdeWaterConsumption = $eqLogic->getEndpointQuery('fridge-water-history', $payload, $deviceTypeCategory);

                    } elseif ($eqLogic->getConfiguration('deviceType') == '201') {
                        $payload = array(
                            'type'       => 'period',
                            'period'     => 'day',
                            'startDate'  => date('Y-m-01'),
                            'endDate'    => date('Y-m-t'),
                            'washerType' => 'M',
                            'twinYn'     => 'N'
                        );
                        $energyHistory = $eqLogic->getEndpointQuery('energy-history', $payload, $deviceTypeCategory);
                        $eqLogic->checkCmdAndUpdateEnergyMonitor($energyHistory);
                    } elseif ($eqLogic->getConfiguration('deviceType') == '401' || $eqLogic->getConfiguration('deviceType') == '406') {
                        $payload = array(
                            'period'    => 'day',
                            'startDate' => date('Y-m-01'),
                            'endDate'   => date('Y-m-t')
                        );
                        $energyHistory = $eqLogic->getEndpointQuery('energy-history', $payload, $deviceTypeCategory);
                        $eqLogic->checkCmdAndUpdateEnergyMonitor($energyHistory);

                        $payload['type'] = 'period';
                        $airPollutionHistory = $eqLogic->getEndpointQuery('air-pollution-history', $payload, $deviceTypeCategory);
                    }
                }
            }
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('fin', __FILE__));
    }

    public function getEndpointQuery($_endpoint, $_postArray, $_deviceTypeCategory) {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__));
        if ($_deviceTypeCategory) {
            $curl = curl_init();
            $headers = array(
                'x-thinq-app-os: ' . lgthinq2::X_THINQ_APP_OS,
                'x-country-code: ' . lgthinq2::getLanguage('uppercase'),
                'sec-fetch-mode: cors',
                'cache-control: no-cache',
                'user-agent: ' . lgthinq2::USER_AGENT,
                'x-thinq-app-ver: ' . lgthinq2::X_THINQ_APP_VER,
                'x-thinq-app-type: ' . lgthinq2::X_THINQ_APP_TYPE,
                'x-language-code: ' . lgthinq2::getLanguage('hyphen'),
                'x-thinq-app-logintype: ' . lgthinq2::X_THINQ_APP_LOGINTYPE,
                'sec-fetch-dest: empty',
                'x-client-id: ' . lgthinq2::getClientId(),
                'x-thinq-app-level: ' . lgthinq2::X_THINQ_APP_LEVEL,
                'sec-fetch-site: cross-site',
                'x-user-no: ' . config::byKey('user_number', __CLASS__),
                'x-service-code: ' . lgthinq2::SVCCODE,
                'accept-language: ' . lgthinq2::getLanguage('hyphen') . ',' . lgthinq2::getLanguage('lowercase') . ';q=0.9',
                'x-message-id: ' . bin2hex(random_bytes(22)),
                'x-emp-token: ' . config::byKey('access_token', __CLASS__),
                'x-origin: app-web-IOS',
                'accept: application/json',
                'accept-encoding: gzip, deflate, br',
                'content-type: application/json;charset=UTF-8',
                'x-thinq-app-pageid: GWM_ENM01_Main/' . $this->getConfiguration('deviceType'), //not sure
                'x-api-key: ' . lgthinq2::XAPIKEY,
                'x-service-phase: ' . lgthinq2::X_SERVICE_PHASE
            );

            $query = urldecode(http_build_query($_postArray));
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('URL : ', __FILE__) . lgthinq2::LGTHINQ2_SERV_URL . 'service/' . strtolower($_deviceTypeCategory) . '/' . $this->getLogicalId() . '/' . $_endpoint . '?' . $query);
            curl_setopt_array($curl, array(
                CURLOPT_URL => lgthinq2::LGTHINQ2_SERV_URL . 'service/' . strtolower($_deviceTypeCategory) . '/' . $this->getLogicalId() . '/' . $_endpoint . '?' . $query,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers
            ));
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

            $response = curl_exec($curl);
            curl_close($curl);
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('response : ', __FILE__) . $response);
            if (!$response) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('erreur : ', __FILE__) . $response);
                return;
            }
            $devices = json_decode($response, true);
            if (!$devices || !isset($devices['resultCode'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($devices));
                return;
            }
            if ($devices['resultCode'] == '0102') {
                lgthinq2::login();
            }
            if ($devices['resultCode'] == '0000' && isset($devices['result'])) {
                return $devices['result'];
            }
            return;
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('fin', __FILE__));
    }

    public function checkCmdAndUpdateWaterConsumptionHistory($_waterHistory) {
        if (isset($_waterHistory['item'])) {
            foreach ($_waterHistory['item'] as $item) {
                if (isset($item['itemDetail'])) {
                    $dateReal = $item['usedDate'] . ' 23:59:00';
                    foreach ($item['itemDetail'] as $detail) {
                        if (isset($detail['waterType']) && isset($detail['waterAmount']) && $detail['waterAmount'] != '0') {
                            $cmd = $this->getCmd('info', 'waterConsumptionType' . $detail['waterType']);
                            if (!is_object($cmd)) {
                                $cmd = new lgthinq2Cmd();
                                $cmd->setEqLogic_id($this->getId());
                                $cmd->setLogicalId('waterConsumptionType' . $detail['waterType']);
                                $cmd->setName(__("Consommation d'eau de type", __FILE__) . ' ' . $detail['waterType']);
                                $cmd->setType('info');
                                $cmd->setSubType('numeric');
                                $cmd->setIsHistorized(1);
                                $cmd->setIsVisible(0);
                                $cmd->setTemplate('dashboard', 'tile');
                                $cmd->setTemplate('mobile', 'tile');
                                $cmd->save();
                            }
                            if ($item['usedDate'] == date('Y-m-d')) {
                                $dateReal = date('Y-m-d H:i:s');
                            }
                            $cmdHistory = history::byCmdIdDatetime($cmd->getId(), $dateReal);
                            if (is_object($cmdHistory) && $cmdHistory->getValue() == $detail['waterAmount']) {
                                log::add(__CLASS__, 'debug', $this->getHumanName() . ' Mesure déjà en historique : Cmd = [' . $cmd->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $detail['waterAmount']);
                            } else {
                                log::add(__CLASS__, 'info', $this->getHumanName() . ' Enregistrement mesure manquante : Cmd = [' . $cmd->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $detail['waterAmount']);
                                $cmd->addHistoryValue($detail['waterAmount'], $dateReal);
                            }
                        }
                    }
                }
            }
        }
    }

    public function checkCmdAndUpdateDoorOpenHistory($_doorHistory) {
        if (isset($_doorHistory['item'])) {
            foreach ($_doorHistory['item'] as $item) {
                if ($item['openTime'] == '0') continue;
                $cmd = $this->getCmd('info', 'doorHistory' . $item['doorType']);
                if (!is_object($cmd)) {
                    $cmd = new lgthinq2Cmd();
                    $cmd->setEqLogic_id($this->getId());
                    $cmd->setLogicalId('doorHistory' . $item['doorType']);
                    $cmd->setName(__("Nombre d'ouverture de porte", __FILE__) . ' ' . $item['doorType']);
                    $cmd->setType('info');
                    $cmd->setSubType('numeric');
                    $cmd->setIsHistorized(1);
                    $cmd->setIsVisible(0);
                    $cmd->setTemplate('dashboard', 'tile');
                    $cmd->setTemplate('mobile', 'tile');
                    $cmd->save();
                }
                $dateReal = $item['usedDate'] . ' ' . gmdate('H:i:s', $item['openTime']);
                if (isset($item['openCount'])) {
                    $cmdHistory = history::byCmdIdDatetime($cmd->getId(), $dateReal);
                    if (is_object($cmdHistory) && $cmdHistory->getValue() == $item['openCount']) {
                        log::add(__CLASS__, 'debug', $this->getHumanName() . ' Mesure déjà en historique : Cmd = [' . $cmd->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['openCount']);
                    } else {
                        log::add(__CLASS__, 'info', $this->getHumanName() . ' Enregistrement mesure manquante : Cmd = [' . $cmd->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['openCount']);
                        $cmd->addHistoryValue($item['openCount'], $dateReal);
                    }
                }
            }
        }
    }

    public function checkCmdAndUpdateEnergyMonitor($_energy) {
        if (isset($_energy['item'])) {
            if ($item['power'] == '0') return;
            $cmdP = $this->getCmd('info', 'programCount');
            if (!is_object($cmdP)) {
                $cmdP = new lgthinq2Cmd();
                $cmdP->setEqLogic_id($this->getId());
                $cmdP->setLogicalId('programCount');
                $cmdP->setName(__("Nombre de programmes par jour", __FILE__));
                $cmdP->setType('info');
                $cmdP->setSubType('numeric');
                $cmdP->setIsHistorized(1);
                $cmdP->setIsVisible(0);
                $cmdP->setTemplate('dashboard', 'tile');
                $cmdP->setTemplate('mobile', 'tile');
                $cmdP->save();
            }
            $cmdC = $this->getCmd('info', 'consumption');
            if (!is_object($cmdC)) {
                $cmdC = new lgthinq2Cmd();
                $cmdC->setEqLogic_id($this->getId());
                $cmdC->setLogicalId('consumption');
                $cmdC->setName(__("Consommation électrique par jour", __FILE__));
                $cmdC->setType('info');
                $cmdC->setSubType('numeric');
                $cmdC->setIsHistorized(1);
                $cmdC->setIsVisible(0);
                $cmdC->setUnite('Wh');
                $cmdC->setTemplate('dashboard', 'tile');
                $cmdC->setTemplate('mobile', 'tile');
                $cmdC->save();
            }
            foreach ($_energy['item'] as $item) {
                $dateReal = $item['usedDate'] . ' 23:59:59';
                //nombre de programmes : count
                if ($item['usedDate'] == date('Y-m-d')) {
                    $dateReal = date('Y-m-d H:i:s');
                }
                if (isset($item['count'])) {
                    $cmdHistory = history::byCmdIdDatetime($cmdP->getId(), $dateReal);
                    if (is_object($cmdHistory) && $cmdHistory->getValue() == $item['count']) {
                        log::add(__CLASS__, 'debug', $this->getHumanName() . ' Mesure déjà en historique : Cmd = [' . $cmdP->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['count']);
                    } else {
                        log::add(__CLASS__, 'info', $this->getHumanName() . ' Enregistrement mesure manquante : Cmd = [' . $cmdP->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['count']);
                        $cmdP->addHistoryValue($item['count'], $dateReal);
                    }
                }
                //consommation : power
                if (isset($item['power'])) {
                    $cmdHistory = history::byCmdIdDatetime($cmdC->getId(), $dateReal);
                    if (is_object($cmdHistory) && $cmdHistory->getValue() == $item['power']) {
                        log::add(__CLASS__, 'debug', $this->getHumanName() . ' Mesure déjà en historique : Cmd = [' . $cmdC->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['power']);
                    } else {
                                  log::add(__CLASS__, 'info', $this->getHumanName() . ' Enregistrement mesure manquante : Cmd = [' . $cmdC->getName() . '] Date = ' . $dateReal . ' => Mesure = ' . $item['power']);
                        $cmdC->addHistoryValue($item['power'], $dateReal);
                    }
                }
            }
        }
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
    public static function getTranslatedNameFromConfig($_name, $_config)
    {
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
    public static function synchronize($_id = false)
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début ', __FILE__) . $_id);
        if (config::byKey('LGE_MEMBERS_URL', __CLASS__, '') == '' || config::byKey('LG_EMPTERMS_URL', __CLASS__, '') == '' || config::byKey('LGACC_SPX_URL', __CLASS__, '') == '') {
            $rep0 = lgthinq2::step0();
        }
        $_id = ($_id !== false) ? (is_object($toto = lgthinq2::byId($_id)) ? $toto->getLogicalId() : '') : '';
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('fin', __FILE__));
        return lgthinq2::getDevices($_id);
    }

    /**
     * Charge les données de configuration à partir d'un fichier.
     *
     * Cette fonction charge les données de configuration à partir d'un fichier JSON en fonction du type fourni.
     *
     * @param string $_type Le type de données de configuration à charger.
     * @return array|null Les données de configuration chargées si elles sont réussies ; sinon, retourne null.
     */
    public static function loadConfigFile($_type, $_dir = '/../../data/')
    {
        //log::add(__CLASS__, 'debug', __FUNCTION__ .' début' . $_type);
        $filename = __DIR__ . $_dir . $_type . '.json';
        if (!file_exists($filename)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Impossible de trouver le fichier de configuration pour l\'équipement ', __FILE__));
            return;
        }
        $content = file_get_contents($filename);
        if (!lgthinq2::isJson($content)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier de configuration ', __FILE__) . $filename . __(' est corrompu', __FILE__));
            return;
        }
        $data = json_decode($content, true);
        if (!is_array($data)/* || !isset($data['commands'])*/) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier de configuration ', __FILE__) . $filename . __(' est invalide', __FILE__));
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
    public static function createEquipement($_capa, $_platform)
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__) . json_encode($_capa));
        if (!isset($_capa['deviceId'])) {
            log::add(__CLASS__, 'error', __FUNCTION__ . ' ' . __('erreur uuid inexistant ', __FILE__) . json_encode($_capa));
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
    public function getDevicesStatus($_repeat = false)
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__) . ' répète : ' . $_repeat);
        lgthinq2::getTokenIsExpired();
        $timestamp = null;
        $platformType = $this->getConfiguration('platformType');
        $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getLogicalId() . '_modelJson');
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
                log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour : ', __FILE__) . json_encode($data));
                foreach ($data as $dkey => $dvalue) {
                    if ($monitoring != '' && array_search($dkey, $monitoring) === true) {
                        $logicalid = array_search($dkey, $monitoring);
                    } else {
                        $logicalid = $dkey;
                    }
                    $valMap = json_decode($this->getConfiguration('valueMapping'),true);
                    if ($logicalid !== false) {
                        $cmdKey = $this->getCmd('info', $logicalid);
                        if (!is_object($cmdKey)) {
                            $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $logicalid);
                        } else {
                            if ($cmdKey->getConfiguration('valueMapping', '')) {
                                if (isset($cmdKey->getConfiguration('valueMapping')[$dvalue])) {
                                    $dvalue = $cmdKey->getConfiguration('valueMapping')[$dvalue];
                                }
                            }
                        }
                        $this->checkValueAndUpdateCmd($logicalid, $dvalue, $timestamp);
                        log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour : ', __FILE__) . $logicalid . __(' à la valeur : ', __FILE__) . $dvalue);
                    }
                }
            }
            return;
        }
        //else thinq2

        $curl = curl_init();
        $headers = lgthinq2::defaultDevicesHeaders();
        //$response = file_get_contents(dirname(__FILE__) . '/../../data/SKY_'.$this->getLogicalId().'.json'); // developper only
        //$response = file_get_contents(dirname(__FILE__) . '/../../data/POC_'.$this->getLogicalId().'.json'); // developper only
        //$response = file_get_contents(dirname(__FILE__) . '/../../data/MAG_'.$this->getLogicalId().'.json'); // developper only

        curl_setopt_array($curl, array(
            CURLOPT_URL => lgthinq2::LGTHINQ2_SERV_URL . 'service/devices/' . $this->getLogicalId(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('getDEVICES HEADERS : ', __FILE__) . json_encode($headers));

        $response = curl_exec($curl);
        curl_close($curl);
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('getDEVICES : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('erreur : ', __FILE__) . $response);
            return;
        }
        $devices = json_decode($response, true);
        if (!$devices || !isset($devices['resultCode'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($devices['resultCode'] != '0000' && $_tokenRefreshed == false) {
            lgthinq2::getDevices($_deviceId, true);
        }

        $modelJson = false;
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/FAY_'.$this->getLogicalId().'.json'),true); // developper only
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/POC_'.$this->getLogicalId().'.json'),true); // developper only
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/SKY_'.$this->getLogicalId().'.json'),true); // developper only
        //$devices = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/MAG_'.$this->getLogicalId().'.json'),true); // developper only

        if (isset($devices['result']['snapshot'])) {
            $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getLogicalId() . '_modelJson');
            $onlineCmd = $this->getCmd('info', 'online');
            /*if (!is_object($onlineCmd)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('AAAAAAAA Commande existe pas ', __FILE__) . json_encode($devices));
                $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, 'online');
            }*/
            if (isset($devices['result']['snapshot']['timestamp'])) {
                $timestamp = date('Y-m-d H:i:s', ($devices['result']['snapshot']['timestamp']/1000));
            }
            /*if (is_object($onlineCmd)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('AAAAAAAA Commande existe ', __FILE__) . $devices['result']['snapshot']['online']);
                $onlineCmd->event($devices['result']['snapshot']['online'], $timestamp);
            //$this->checkAndUpdateCmd('online', $devices['result']['online'], $timestamp);
            }*/
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
                log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour : ', __FILE__) . $refStateId . __(' à la valeur : ', __FILE__) . $refStateValue . __(' et au temps : ', __FILE__) . $timestamp);
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
    public function changeMonitorStatus($_action)
    {
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
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('URL : ', __FILE__) . lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiMon' );
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DATA : ', __FILE__) . json_encode($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('HEADERS : ', __FILE__) . json_encode($headers));

        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiMon', json_encode($data), $headers);

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('RESPONSE : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('erreur : ', __FILE__) . $response);
            return;
        }
        $work = json_decode($response, true);
        if (!$work || !isset($work[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête  ', __FILE__) . json_encode($work));
            return;
        }
        if (isset($work[lgthinq2::DATA_ROOT]['returnCd']) && $work[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de code ', __FILE__) . $work[lgthinq2::DATA_ROOT]['returnCd'] . ' ' . $work[lgthinq2::DATA_ROOT]['returnMsg']);
            if ($work[lgthinq2::DATA_ROOT]['returnCd'] == '0102') {
                config::save('jsessionId', lgthinq2::step6(), __CLASS__);
            }
            return;
        }
        if (isset($work[lgthinq2::DATA_ROOT]['workId'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Requête réussie ', __FILE__) . json_encode($work));
            $this->setConfiguration('workId', $work[lgthinq2::DATA_ROOT]['workId'])->save();
        } else {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('workId non présent ', __FILE__) . json_encode($work));
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
    public function pollMonitorStatus($_repeat = false)
    {
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
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('URL : ', __FILE__) . lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiResult' );
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DATA : ', __FILE__) . json_encode($data));
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('HEADERS : ', __FILE__) . json_encode($headers));

        $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiResult', json_encode($data, JSON_PRETTY_PRINT), $headers);
        //$response = json_decode(file_get_contents(dirname(__FILE__) . '/../../data/OTH_'.$this->getLogicalId().'.json'),true); // developper only

//{"lgedmRoot":{"returnCd":"0000","returnMsg":"OK","workList":{"deviceId":"d27af0a0-7149-11d3-80aa-044eaf4149f1","deviceState":"E","returnCode":"0106","stateCode":"N","workId":"n-d27af0a0-7149-11d3-80aa-044eaf4149f1"}}}

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('response : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('erreur : ', __FILE__) . $response);
            return;
        }
        $rti = json_decode($response, true);
        $online = false;
        if (!$rti || !isset($rti[lgthinq2::DATA_ROOT]['returnCd'])) {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($rti[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de code ', __FILE__) . $rti[lgthinq2::DATA_ROOT]['returnCd'] . ' ' . $rti[lgthinq2::DATA_ROOT]['returnMsg']);
            if ($rti[lgthinq2::DATA_ROOT]['returnCd'] == '0102' && $_repeat == false) {
                config::save('jsessionId', lgthinq2::step6(), __CLASS__);
                return lgthinq2::pollMonitorStatus(true);
            }
            return;
        }
        if (!isset($rti[lgthinq2::DATA_ROOT]['workList'])) {
            $this->setConfiguration('workId', '')->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('WorkList non existant ', __FILE__) . json_encode($rti));
            return;
        }
        $workList = $rti[lgthinq2::DATA_ROOT]['workList'];

        if (!isset($workList['returnCode']) || $workList['returnCode'] == '0106') {
            $nbDisconnects = (int)$this->getConfiguration('nbDisconnections', 0);
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('returnCode null ou 0106, $nbDisconnects ', __FILE__) . $nbDisconnects);
            if ($nbDisconnects >= 1) {
                $this->setConfiguration('workId', '');
                $this->setConfiguration('nbDisconnections', 0)->save();
                $this->getDevicesStatus(true);
            } else {
                $this->setConfiguration('nbDisconnections', $nbDisconnects + 1)->save();
            }
            return;
        }

        /*if (!isset($workList['returnCode']) && isset($workList['stateCode'])) {
            if (in_array($workList['stateCode'], array('P','W','F','N')) && $_repeat == false) { // E? N?
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' : returnCode non existant ' . json_encode($rti));
                $this->setConfiguration('workId', '')->save();
                $this->getDevicesStatus(true);
                return;
            }
        }*/
        if (isset($workList['returnCode']) && $workList['returnCode'] != '0000') {
            if ($workList['returnCode'] == '0100' && $_repeat == false) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('returnCode non existant ', __FILE__) . json_encode($rti));
                $this->setConfiguration('workId', '')->save();
                $this->getDevicesStatus(true);
            }
            return;
        }
        if (isset($workList['deviceState'])) {
            $online = $workList['deviceState'] == 'E' ? true : false;
        }
        $onlineCmd = $this->getCmd('info', 'online');
        if (!is_object($onlineCmd)) {
            $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getLogicalId() . '_modelJson');
            $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, 'online');
        }
        if (is_object($onlineCmd)) {
            $onlineCmd->event($online);
        }
        if (isset($workList['returnData']) && $workList['format'] == 'B64') {
            $returnUndecodedData = $workList['returnData'];
            if ($this->getConfiguration('nbDisconnections', 0) > 0) {
                $this->setConfiguration('nbDisconnections', 0)->save(); // reset nb disconnections
            }
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Requête réussie ', __FILE__) . json_encode($returnUndecodedData));
            if ($workList['format'] == 'B64') {
                if ($this->getConfiguration('MonitoringType') == 'JSON') {
                    return json_decode(base64_decode($returnUndecodedData), true);
                } else if ($this->getConfiguration('MonitoringType') == 'BINARY(BYTE)') {
                    return self::decodeMonitorBinary($returnUndecodedData, $this->getConfiguration('Monitoring'));
                }
            } else {
                return $returnUndecodedData; // else put it on string info cmd
            }
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
    public function getDeviceRtiControl($_cmd, $_cmdOpt, $_value)
    {
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

        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('response : ', __FILE__) . $response);
        if (!$response) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('erreur : ', __FILE__) . $response);
            return;
        }
        $rti = json_decode($response, true);
        if (!$rti || !isset($rti[lgthinq2::DATA_ROOT])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($devices));
            return;
        }
        if ($rti[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Erreur de code ', __FILE__) . json_encode($rti));
            return;
        }
        if (isset($rti[lgthinq2::DATA_ROOT]['returnData']) && $rti[lgthinq2::DATA_ROOT]['format'] == 'B64') {
            $returnUndecodedData = $rti[lgthinq2::DATA_ROOT]['returnData'];
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Requête réussie ', __FILE__) . json_encode($returnUndecodedData));
            if ($rti[lgthinq2::DATA_ROOT]['format'] == 'B64') {
                if ($this->getConfiguration('MonitoringType') == 'JSON') {
                    return json_decode(base64_decode($returnUndecodedData), true);
                } else if ($this->getConfiguration('MonitoringType') == 'BINARY(BYTE)') {
                    return self::decodeMonitorBinary($returnUndecodedData, $this->getConfiguration('Monitoring'));
                }
            } else {
                return $returnUndecodedData; // else put it on string info cmd
            }
        }
    }

    /**
     * Méthode appellée avant la création de l'objet
     * Active et affiche l'objet
     */
    public function preInsert()
    {
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    /**
     * Méthode appellée après la création de l'objet
     * Ajoute la commande refresh
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
    public function refresh()
    {
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('début', __FILE__));
        $this->getDevicesStatus();
        log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('fin', __FILE__));
    }

    /**
     * Recherche la commande dans le fichier de config
     * @param		string		$_key			Clé de la commande
     * @return		object		$command		Commande trouvée dans le fichier
     */
    public function checkAndCreateCmdFromConfigFile($_configData, $_key)
    {
        foreach ($_configData['commands'] as $command) {
            if ($command['logicalId'] == $_key || $command['configuration']['ctrlKey'] == $_key) {
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
    public function getLangJson($_type, $_langFileUri = '', $_langFileVer)
    {
        $curVersion = $this->getConfiguration($_type . 'Ver', '');
        $file = __DIR__ . '/../../data/' . $this->getLogicalId() . '_' . $_type . '.json';
        if ($curVersion != '' && version_compare($curVersion, $_langFileVer, '>=') && is_file($file)) {
            $config = file_get_contents($file);
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier ',__FILE__) . $_type . __(' existe à la version ', __FILE__) . $curVersion);
        } else {
            if ($_langFileUri == '') {
                return false;
            }
            $config = file_get_contents($_langFileUri);
            $config = lgthinq2::cleanJson($config);
            file_put_contents($file, $config);
            $this->setConfiguration($_type . 'Ver', $_langFileVer)->save();
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier ',__FILE__) . $_type . __(' existe pas à la version ', __FILE__) . $curVersion);
        }
        if (!lgthinq2::isJson($config)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier ',__FILE__) . $_type . __(' est corrompu', __FILE__));
            return false;
        }
        $data = json_decode($config, true);
        if (!is_array($data)) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier ',__FILE__) . $_type . __(' est invalide', __FILE__));
            return false;
        }
        if (!isset($data['pack'])) {
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('"Pack" n\'existe pas dans le fichier ',__FILE__) . $_type);
            return false;
        }
        //log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Fichier de langue', __FILE__) . json_encode($data['pack']));
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
    public function createCmdFromModelAndLangFiles($_modelJsonUri, $_modelJsonVer, $_refState, $langPack, $refState = null)
    {
        if ($_modelJsonUri != '') {
            $curVersion = $this->getConfiguration('modelJsonVer', '0.0');
            $file = __DIR__ . '/../../data/' . $this->getLogicalId() . '_modelJson.json';
            if (version_compare($curVersion, $_modelJsonVer, '>=') && is_file($file)) {
                $config = file_get_contents($file);
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier modelJson existe à la version ', __FILE__) . $curVersion);
            } else {
                $config = file_get_contents($_modelJsonUri);
                $config = lgthinq2::cleanJson($config);
                file_put_contents($file, $config);
                $this->setConfiguration('modelJsonVer', $_modelJsonVer)->save();
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier modelJson n\'existait pas à la version ', __FILE__) . $curVersion);
            }
            if (!lgthinq2::isJson($config)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier de configuration est corrompu', __FILE__));
            }
            $data = json_decode($config, true);
            if (!is_array($data)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Le fichier de configuration est invalide', __FILE__));
            }
            //save translation model into json file
            //file_put_contents(__DIR__ . '/../../data/' . $this->getLogicalId() . '.json', json_encode($data));

            if (isset($data['Value'])) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DEBUGGGG Value ', __FILE__) . json_encode($data['Value']));
                $commands = array();
                foreach ($data['Value'] as $key => $value) {
                    if (isset($data['Monitoring']['type']) && $data['Monitoring']['type'] == "THINQ2") {
                        if (!isset($_refState[$key])) {
                            foreach ($data['Monitoring']['protocol'] as $protoKey => $protoValue) {
                                if ($protoValue == $key) {
                                    $key = $protoKey;
                                }
                            }
                        }
                    }
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
                    if (isset($langPack[$name]) && $langPack[$name] != '') {
                        $name = $langPack[$name];
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
                            'valueMapping' => self::translateValueMapping(($value['value_mapping'] ?? $value['option']), $langPack),
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

                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DEBUGGGG MonitoringValue ', __FILE__) . json_encode($data['MonitoringValue']));
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
                    if (isset($langPack[$name]) && $langPack[$name] != '') {
                        $name = $langPack[$name];
                    } else {
                        $name = $key;
                    }

                    //course for dryer/washer action cmds
                    $courseArray = null;
                    if (isset($value['ref'])) {
                        if (isset($data[$value['ref']])) {
                            foreach ($data[$value['ref']] as $courseId => $courseValue) {
                                $newArray = call_user_func_array('array_merge', array_map(function($item) {
                                    return [$item['value'] => $item['default']];
                                }, $courseValue['function']));
                                $courseArray[$courseId] = array(
                                    'course'  => $courseValue['Course'],
                                    'type'    => $courseValue['courseType'],
                                    'value'   => $courseValue['courseValue'],
                                    'name'    => isset($langPack[$courseValue['name']]) ? $langPack[$courseValue['name']] : $courseValue['name'],
                                    'default' => $newArray
                                );
                            }
                        }
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
                            'valueMapping' => self::translateValueMapping($value['valueMapping'], $langPack),
                            'targetKey' => $targetKey,
                            'targetKeyValues' => $targetKeyValues,
                            'tempUnitValue' => $tempUnitValue,
                            'ref' => $courseArray ?? null
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
                if (isset($data['Monitoring']['type']) && $data['Monitoring']['type'] == 'JSON') {
                    if (isset($data['Monitoring']['protocol'])) {
                        $monit = array();
                        foreach ($data['Monitoring']['protocol'] as $protocol) {
                            $monit[$protocol['value']] = $protocol['path'];
                        }
                        $this->setConfiguration('Monitoring', $monit);
                    }
                } else if (isset($data['Monitoring']['type']) && $data['Monitoring']['type'] == 'BINARY(BYTE)') {
                    if (isset($data['Monitoring']['protocol'])) {
                        $monit = array();
                        foreach ($data['Monitoring']['protocol'] as $protocol) {
                            $monit[$protocol['value']] = array(
                                'value' => $protocol['value'],
                                'startByte' => $protocol['startByte'],
                                'length' => $protocol['length']
                            );
                        }
                        $this->setConfiguration('Monitoring', $monit);
                    }
                }
                $this->setConfiguration('MonitoringType', $data['Monitoring']['type'])->save();
            }

            if (isset($data['ControlWifi'])) {
                $commands = array();
                if (isset($data['ControlWifi']['type']) && $data['ControlWifi']['type'] == 'JSON' && isset($data['ControlWifi']['action'])) {
                    foreach ($data['ControlWifi']['action'] as $actionName => $actionConfig) {
                        $listValue = null;
                        $subType = 'other';
                        $updateCmdToValue = null;
                        if (preg_match_all('/{{(.*?)}}/', $actionConfig['value'], $matches)) {
                            foreach ($matches[1] as $paramKey) {
                                log::add(__CLASS__, 'debug', 'CONTROLWIFI match actionName ' .$actionName . " key : " . $paramKey);
                                $listValue = null;
                                $updateCmdId = null;
                                $cmdInfo = $this->getCmd('info', $paramKey);
                                if (!isset($data['Value'][$paramKey]) && isset($data['Value']['support.'.$paramKey])) {
                                    $data['Value'][$paramKey] = $data['Value']['support.'.$paramKey];
                                }
                                if (isset($data['Value'][$paramKey])) {
                                    log::add(__CLASS__, 'debug', 'CONTROLWIFI match value1 ' . $paramKey);

                                    if ($data['Value'][$paramKey]['type'] == 'String') {
                                        $subType = 'message';
                                        $updateCmdToValue = '#message#';
                                        $updateCmdId = is_object($cmdInfo) ? $cmdInfo->getId() : null;
                                    } elseif ($data['Value'][$paramKey]['type'] == 'Enum') {
                                        $subType = 'select';
                                        $updateCmdToValue = '#select#';
                                        if (isset($data['Value'][$paramKey]['option'])) {
                                            foreach ($data['Value'][$paramKey]['option'] as $optionKey => $optionValue) {
                                                if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                    if (isset($langPack[$optionValue])) {
                                                        $optionValue = $langPack[$optionValue];
                                                    }
                                                }
                                                $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                            }
                                            $listValue = substr($listValue, 0, -1);
                                        }
                                        $updateCmdId = is_object($cmdInfo) ? $cmdInfo->getId() : null;
                                    } elseif ($data['Value'][$paramKey]['type'] == 'Range') {
                                        $subType = 'slider';
                                        $updateCmdToValue = '#slider#';
                                        $updateCmdId = is_object($cmdInfo) ? $cmdInfo->getId() : null;
                                    }
                                    $configurationString = str_replace('{{'.$paramKey.'}}', $updateCmdToValue, $actionConfig['value']);
                                    $configurationString = str_replace(array('{{','}}'), '#', $configurationString);
                                }
                                if ($actionConfig['cmdOpt'] == 'Get') {
                                    $nameCKey = str_replace('Get', '', $actionName, $iCKey);
                                    $commands[] = array(
                                        'name' => $nameCKey . ' ' . $paramKey,
                                        'type' => 'info',
                                        'logicalId' => $actionName . $paramKey,
                                        'subType' => 'string',
                                        'configuration' => array(
                                            'cmd' => $actionConfig['cmd'],
                                            'cmdOpt' => $actionConfig['cmdOpt'],
                                            'value' => $configurationString,
                                            'encode' => $actionConfig['encode'],
                                            'listValue' => $listValue,
                                            'controlType' => $data['ControlWifi']['type'],
                                            'paramKey' => $paramKey
                                        )
                                    );
                                }
                                $commands[] = array(
                                    'name' => ($iCKey?$actionName.' '.config::genKey(2):$actionName) . ' ' . $paramKey,
                                    'type' => 'action',
                                    'logicalId' => $actionName . $paramKey,
                                    'subType' => $subType,
                                    'configuration' => array(
                                        'cmd' => $actionConfig['cmd'],
                                        'cmdOpt' => $actionConfig['cmdOpt'],
                                        'value' => $configurationString,
                                        'encode' => $actionConfig['encode'],
                                        'listValue' => $listValue,
                                        'updateLGCmdId' => $updateCmdId,
                                        'updateLGCmdToValue' => $updateCmdToValue,
                                        'controlType' => $data['ControlWifi']['type'],
                                        'paramKey' => $paramKey,
                                        'listValueSelected' => (is_object($cmdInfo) && $cmdInfo->getSubType() == 'binary' ? false : true)
                                    ),
                                    'value' => $updateCmdId
                                );
                            }

                        } elseif (preg_match('/{(.*?)}/', $actionConfig['value'], $matches)) {
                            $paramKey = $matches[1];
                            if (!isset($data['Value'][$paramKey]) && isset($data['Value']['support.'.$paramKey])) {
                                $data['Value'][$paramKey] = $data['Value']['support.'.$paramKey];
                            }
                            if (isset($data['Value'][$paramKey])) {
                                log::add(__CLASS__, 'debug', 'CONTROLWIFI match value1 ' . $matches[1]);
                                $listValue = null;
                                $updateCmdId = null;
                                if ($data['Value'][$paramKey]['type'] == 'String') {
                                    $subType = 'message';
                                    $updateCmdToValue = '#message#';
                                    $actionConfig['value'] = str_replace('{'.$matches[1].'}', '#message#', $actionConfig['value']);
                                    $updateCmdId = is_object($this->getCmd('info', $paramKey)) ? $this->getCmd('info', $paramKey)->getId() : null;
                                } elseif ($data['Value'][$paramKey]['type'] == 'Enum') {
                                    $subType = 'select';
                                    $updateCmdToValue = '#select#';
                                    if (isset($data['Value'][$paramKey]['option'])) {
                                        foreach ($data['Value'][$paramKey]['option'] as $optionKey => $optionValue) {
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
                                    $updateCmdId = is_object($this->getCmd('info', $paramKey)) ? $this->getCmd('info', $paramKey)->getId() : null;
                                } elseif ($data['Value'][$paramKey]['type'] == 'Range') {
                                    $subType = 'slider';
                                    $updateCmdToValue = '#slider#';
                                    $actionConfig['value'] = str_replace('{'.$matches[1].'}', '#slider#', $actionConfig['value']);
                                    $updateCmdId = is_object($this->getCmd('info', $paramKey)) ? $this->getCmd('info', $paramKey)->getId() : null;
                                }
                                //log::add(__CLASS__, 'debug', 'CONTROLWIFI match value3 ' . $matches[1]);
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
                                'name' => ($iCKey?$actionName.' '.config::genKey(2):$actionName),
                                'type' => 'action',
                                'logicalId' => $actionName,
                                'subType' => $subType,
                                'configuration' => array(
                                    'cmd' => $actionConfig['cmd'],
                                    'cmdOpt' => $actionConfig['cmdOpt'],
                                    'value' => $actionConfig['value'],
                                    'encode' => $actionConfig['encode'],
                                    'listValue' => $listValue,
                                    'updateLGCmdToValue' => $updateCmdToValue,
                                    'updateLGCmdId' => $updateCmdId,
                                    'listValueSelected' => (is_object($this->getCmd('info', $paramKey) && $this->getCmd('info', $paramKey)->getSubType() == 'binary') ? false : true)
                                ),
                                'value' => $updateCmdId
                            );
                        }
                    }
                } else if (isset($data['ControlWifi']['type']) && $data['ControlWifi']['type'] == 'BINARY(BYTE)' && isset($data['ControlWifi']['action'])) {
                    foreach ($data['ControlWifi']['action'] as $actionName => $actionConfig) {
                        $subType = 'other';
                        $updateCmdToValue = null;
                        $actionConfigValue = isset($actionConfig['data']) ? $actionConfig['data'] : $actionConfig['value'];
                        preg_match_all('/\b\w+\b/', $actionConfigValue, $matchesAll);
                        $this->setConfiguration('paramDataList', $matchesAll[0])->save();
                        if (preg_match_all('/{{(.*?)}}/', $actionConfigValue, $matches)) {
                            foreach ($matches[1] as $paramKey) {
                                $listValue = null;
                                if (!isset($data['Value'][$paramKey]) && isset($data['Value']['support.'.$paramKey])) {
                                    $data['Value'][$paramKey] = $data['Value']['support.'.$paramKey];
                                }
                                if (isset($data['Value'][$paramKey])) {
                                    if ($data['Value'][$paramKey]['type'] == 'String') {
                                        $subType = 'message';
                                        $updateCmdToValue = '#message#';
                                        $actionConfig['value'] = str_replace('{{'.$paramKey.'}}', '#message#', $actionConfig['value']);
                                    } elseif ($data['Value'][$paramKey]['type'] == 'Enum') {
                                        $subType = 'select';
                                        $updateCmdToValue = '#select#';
                                        if (isset($data['Value'][$paramKey]['option'])) {
                                            foreach ($data['Value'][$paramKey]['option'] as $optionKey => $optionValue) {
                                                if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                    if (isset($langPack[$optionValue])) {
                                                        $optionValue = $langPack[$optionValue];
                                                    }
                                                }
                                                $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                            }
                                            $listValue = substr($listValue, 0, -1);
                                        }
                                        $actionConfig['value'] = str_replace('{{'.$paramKey.'}}', '#select#', $actionConfig['value']);

                                    } elseif ($data['Value'][$paramKey]['type'] == 'Range') {
                                        $subType = 'slider';
                                        $updateCmdToValue = '#slider#';
                                        $actionConfig['value'] = str_replace('{{'.$paramKey.'}}', '#slider#', $actionConfig['value']);
                                    }
                                }
                                if ($actionConfig['cmdOpt'] == 'Get') {
                                    $nameCKey = str_replace('Get', '', $actionName, $iCKey);
                                    $commands[] = array(
                                        'name' => $nameCKey . ' ' . $paramKey,
                                        'type' => 'info',
                                        'logicalId' => $actionName . $paramKey,
                                        'subType' => 'string',
                                        'configuration' => array(
                                            'cmd' => $actionConfig['cmd'],
                                            'cmdOpt' => $actionConfig['cmdOpt'],
                                            'value' => $actionConfig['value'],
                                            'encode' => $actionConfig['encode'],
                                            'listValue' => $listValue,
                                            'controlType' => $data['ControlWifi']['type'],
                                            'paramKey' => $paramKey
                                        )
                                    );
                                }
                                $commands[] = array(
                                    'name' => ($iCKey?$actionName.' '.config::genKey(2):$actionName) . ' ' . $paramKey,
                                    'type' => 'action',
                                    'logicalId' => $actionName . $paramKey,
                                    'subType' => $subType,
                                    'configuration' => array(
                                        'cmd' => $actionConfig['cmd'],
                                        'cmdOpt' => $actionConfig['cmdOpt'],
                                        'value' => $actionConfig['value'],
                                        'encode' => $actionConfig['encode'],
                                        'listValue' => $listValue,
                                        'updateLGCmdToValue' => $updateCmdToValue,
                                        'controlType' => $data['ControlWifi']['type'],
                                        'paramKey' => $paramKey
                                    )
                                );
                            }
                        } elseif (preg_match_all('/{(.*?)}/', $actionConfigValue, $matches)) {
                            foreach ($matches[1] as $paramKey) {
                                $listValue = null;
                                if (!isset($data['Value'][$paramKey]) && isset($data['Value']['support.'.$paramKey])) {
                                    $data['Value'][$paramKey] = $data['Value']['support.'.$paramKey];
                                }
                                if (isset($data['Value'][$paramKey])) {
                                    if ($data['Value'][$paramKey]['type'] == 'String') {
                                        $subType = 'message';
                                        $updateCmdToValue = '#message#';
                                        $actionConfig['value'] = str_replace('{'.$paramKey.'}', '#message#', $actionConfig['value']);
                                    } elseif ($data['Value'][$paramKey]['type'] == 'Enum') {
                                        $subType = 'select';
                                        $updateCmdToValue = '#select#';
                                        if (isset($data['Value'][$paramKey]['option'])) {
                                            foreach ($data['Value'][$paramKey]['option'] as $optionKey => $optionValue) {
                                                if (is_array($langPack) && isset($optionValue) && (strpos($optionValue, '@') === 0)) {
                                                    if (isset($langPack[$optionValue])) {
                                                        $optionValue = $langPack[$optionValue];
                                                    }
                                                }
                                                $listValue .= str_replace('|','-', $optionKey) . '|' . $optionValue . ';';
                                            }
                                            $listValue = substr($listValue, 0, -1);
                                        }
                                        $actionConfig['value'] = str_replace('{'.$paramKey.'}', '#select#', $actionConfig['value']);

                                    } elseif ($data['Value'][$paramKey]['type'] == 'Range') {
                                        $subType = 'slider';
                                        $updateCmdToValue = '#slider#';
                                        $actionConfig['value'] = str_replace('{'.$paramKey.'}', '#slider#', $actionConfig['value']);
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
                                            'listValue' => $listValue,
                                            'controlType' => $data['ControlWifi']['type']
                                        )
                                    );
                                }
                                $commands[] = array(
                                    'name' => ($iCKey?$actionName.' '.config::genKey(2):$actionName),
                                    'type' => 'action',
                                    'logicalId' => $actionName,
                                    'subType' => $subType,
                                    'configuration' => array(
                                        'cmd' => $actionConfig['cmd'],
                                        'cmdOpt' => $actionConfig['cmdOpt'],
                                        'value' => $actionConfig['value'],
                                        'encode' => $actionConfig['encode'],
                                        'listValue' => $listValue,
                                        'updateLGCmdToValue' => $updateCmdToValue,
                                        'controlType' => $data['ControlWifi']['type']
                                    )
                                );
                            }
                        }
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
                        } else if ($controlKey == 'vtCtrl') {
                            $deviceTypeConfigFile = lgthinq2::loadConfigFile($this->getConfiguration('deviceType'), '/../../core/config/devices/');
                            $this->checkAndCreateCmdFromConfigFile($deviceTypeConfigFile, $controlKey);

                        } else {
                            if ($controlValue['command'] == 'Get') {
                                $nameCKey = str_replace(array('Get', 'get'), '', $controlKey, $iCKey);
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
                                'name' => ($iCKey?$controlKey.' '.config::genKey(2):$controlKey),
                                'type' => 'action',
                                'logicalId' => $controlKey,
                                'subType' => 'other',
                                'configuration' => array(
                                    'ctrlKey' => $controlKey,
                                    'cmd' => $controlValue['command'],
                                    'dataSetList' => (in_array($controlKey, array('WMStart', 'WMDownload', 'WMOff', 'WMStop', 'WMWakeup', 'vtCtrl', 'ospWakeup', 'courseSetting'))?$controlValue['data']:null)
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
                                if (!isset($data['Value'][$key]) && isset($data['Value']['support.'.$key])) {
                                    $data['Value'][$key] = $data['Value']['support.'.$key];
                                }
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
                $actCmd->setConfiguration('listValueSelected', $cmdInfo->getSubType() == 'binary' ? false : true);
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
    public function checkValueAndUpdateCmd($refStateId, $refStateValueArray, $timestamp)
    {
        $cmd = array();
        $cmdInfo = $this->getCmd('info', $refStateId);
        if (is_object($cmdInfo)) {
            if ($cmdInfo->getUnite() == '°C') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['CELSIUS'];
                if (isset($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray])) {
                    //return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                    $cmdInfo->event($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                }
            } elseif ($cmdInfo->getUnite() == '°F') {
                $tkv = $cmdInfo->getConfiguration('targetKey')['tempUnit']['FAHRENHEIT'];
                if (isset($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray])) {
                    //return $this->checkAndUpdateCmd($refStateId, $cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                    $cmdInfo->event($cmdInfo->getConfiguration('targetKeyValues')[$tkv][$refStateValueArray]['label'], $timestamp);
                }
            }
            if ($cmdInfo->getSubType() == 'binary') {
                log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour before : ', __FILE__) . $refStateId . __(' à la valeur : ', __FILE__) . $refStateValueArray . ' et type ' . gettype($refStateValueArray));
                $refStateValueArray = lgthinq2Cmd::getStringToBinaryValues($refStateValueArray);
                log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour after : ', __FILE__) . $refStateId . __(' à la valeur : ', __FILE__) . $refStateValueArray);
            }
            if ($cmdInfo->getConfiguration('originalType') == 'Array') {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DEBUGGGG cmd info Array ', __FILE__) . json_encode($refStateValueArray));
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
                                    $cmd[$arrKey]->event($arrVal, $timestamp);
                                }
                            }
                        }
                    }
                    $refStateValueArray = json_encode($refStateValueArray);
                }
            }
            log::add(__CLASS__, 'info', __FUNCTION__ . ' ' . __('commande mise à jour : ', __FILE__) . $refStateId . __(' à la valeur : ', __FILE__) . $refStateValueArray);
            $cmdInfo->event($refStateValueArray, $timestamp);
        }
        //return $this->checkAndUpdateCmd($refStateId, $refStateValueArray, $timestamp);
    }

    /**
     * Vérifie si l'équipement est connecté.
     *
     * Cette fonction vérifie si l'équipement est connecté en interrogeant l'état de la commande 'online'.
     *
     * @return bool True si l'équipement est connecté ; sinon, false.
     */
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
            log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Commande online inexistante : ', __FILE__) . $this->getConfiguration('deviceType', '') . ' ' . $this->getLogicalId());
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
    public function createCommand($_properties, $_cmdInfo = null)
    {
        if ($this->getIsEnable()) {
            $type = (!isset($_properties['type'])?(!$_cmdInfo?'info':'action'):$_properties['type']);
            $cmd = $this->getCmd($type, $_properties['logicalId']);
            foreach ($this->getCmd() as $aCmd) {
                if ($aCmd->getName() == $_properties['name'] //si le nom est le même
                    && $aCmd->getLogicalId() != $_properties['logicalId']) { // mais le logicalId différent
                    $_properties['name'] .= config::genKey(2);   // on ajoute un nombre aléatoire au nom
                } else if ($_properties['name'] == 'N/A' && $aCmd->getLogicalId() != $_properties['logicalId']) { // si nom == NA
                    $_properties['name'] = $_properties['logicalId'];   // on utilise en nom le logicalId
                }
            }
            if (!is_object($cmd)) {
                log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('DEBUGGGG $_properties ', __FILE__) . is_object($cmd) . ' => ' . $_properties['logicalId']);
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
    public function getImage()
    {
        $file = 'plugins/lgthinq2/core/config/img/' . $this->getConfiguration('deviceCode') . '.webp';
        if (is_file(dirname(__FILE__) . '/../../../../' . $file)) {
            return $file;
        }
        $file1 = 'plugins/lgthinq2/core/config/img/' . $this->getConfiguration('deviceType') . '.png';
        if (is_file(dirname(__FILE__) . '/../../../../' . $file1)) {
            return $file1;
        }
        return 'plugins/lgthinq2/plugin_info/config/img/' . $this->getConfiguration('thumbnail', '../../../plugin_info/lgthinq2_icon.png');
    }

    /**
     * Récupère une commande à partir de son ID logique.
     *
     * Cette fonction récupère les informations sur la commande correspondant à l'ID logique spécifié.
     *
     * @param string $_logicalId L'ID logique de la commande.
     * @return lgthinq2Cmd|false Les informations sur la commande si elles existent ; sinon, retourne false.
     */
    public function getCmdInfo($_logicalId = '')
    {
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
    public function toHtml($_version = 'dashboard')
    {
        $deviceType = $this->getConfiguration('deviceType');
        if ($this->getDisplay('widgetTmpl', 0) == 0) {
            return parent::toHtml($_version);
        }
        if (!in_array($deviceType, array(201, 202, 221, 222))) {
            return parent::toHtml($_version);
        }
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $_version = jeedom::versionAlias($_version);

        if ($deviceType == 201 || $deviceType == 221) {
            $course = $this->getCmd('info', 'courseFL24inchBaseTitan');
            $smartCourse = $this->getCmd('info', 'smartCourseFL24inchBaseTitan');
        } elseif ($deviceType == 202 || $deviceType == 222) {
            $course = $this->getCmd('info', 'courseDryer24inchBase');
            $smartCourse = $this->getCmd('info', 'smartCourseDryer24inchBase');
        }

        $refList = array();
        if (is_object($course) && $course->getConfiguration('ref', '') != '') {
            foreach ($course->getConfiguration('ref') as $refKey => $refVal) {
                if (isset($refVal['default'])) {
                    $refList = array_merge($refList, array_keys($refVal['default']));
                }
            }
        }
        $refList = array_values(array_unique($refList));
        if (is_object($course))  $refList[] = $course->getLogicalId();
        if (is_object($smartCourse))  $refList[] = $smartCourse->getLogicalId();

        $cmd_html = '';
        foreach ($this->getCmd('info', null) as $cmd) {

            $valueMapping = $cmd->getConfiguration('valueMapping', '');
            $refs = $cmd->getConfiguration('ref', '');
            $replace['#cmd_' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            $replace['#cmd_' . $cmd->getLogicalId() . '_name#'] = ($cmd->getDisplay('icon') != '') ? $cmd->getDisplay('icon') : $cmd->getName();
            $replace['#cmd_' . $cmd->getLogicalId() . '_value#'] = $cmd->execCmd();
            $replace['#cmd_' . $cmd->getLogicalId() . '_icon#'] = $cmd->getDisplay('icon', '');
            $replace['#cmd_' . $cmd->getLogicalId() . '_display#'] = $cmd->getIsVisible() == 0?'hidden':'';
            $replace['#cmd_' . $cmd->getLogicalId() . '_nameDisplay#'] = $cmd->getDisplay('showNameOn' . $_version, 1) != 0?'':'hidden';
            if ($cmd->getConfiguration('maxValue', '') != '') {
                $replace['#cmd_' . $cmd->getLogicalId() . '_maxValue#'] = $cmd->getConfiguration('maxValue');
            }

            if (!in_array($cmd->getLogicalId(), $refList)) {
			    $cmd_html .= $cmd->toHtml($_version, '');
            }

            if ($valueMapping != '') {
                if (isset($valueMapping['min']) || isset($valueMapping['max']) || isset($valueMapping['step'])) {
                    $replace['#cmd_' . $cmd->getLogicalId() . '_min#'] = $valueMapping['min'];
                    $replace['#cmd_' . $cmd->getLogicalId() . '_max#'] = $valueMapping['max'];
                } else {
                    $listOption = '';
                    $foundSelect = false;
                    foreach ($valueMapping as $elementId => $elementValue) {
                        $elementValue['label'] = ($elementValue['label'] == '')?'['.$elementId.']':$elementValue['label'];
                        if ($cmd->execCmd() === $elementId || $cmd->execCmd() === $elementValue['index']) {
                            $listOption .= '<option value="' . $elementId . '" selected="true">' . $elementValue['label'] . '</option>';
                            $foundSelect = true;
                        } else {
                            $listOption .= '<option value="' . $elementId . '">' . $elementValue['label'] . '</option>';
                        }
                    }
                    if (!$foundSelect) {
                        $listOption = '<option value="">{{Aucun}}</option>' . $listOption;
                    }
                    $replace['#cmd_' . $cmd->getLogicalId() . '_valueMapping#'] = $listOption;
                }
            }
            if ($refs != '') {
                $replace['#cmd_' . $cmd->getLogicalId() . '_refInfo#'] = str_replace('\'', ' ',json_encode($refs));
                $listOption = '';
                $foundSelect = false;
                foreach ($refs as $refId => $refValue) {
                    $listOption .= '<option value="' . $refId . '">' . $refValue['name'] . ' [' . $refId . ']</option>';
                }
                if (!$foundSelect) {
                    $listOption = '<option value="">{{Aucun}}</option>' . $listOption;
                }
                $replace['#cmd_' . $cmd->getLogicalId() . '_valueMapping#'] = $listOption;
            }
            if ($cmd->getDisplay('showIconAndName' . $_version, 0) == 1) {
                $replace['#cmd_' . $cmd->getLogicalId() . '_name#'] = $cmd->getDisplay('icon') . ' ' . $cmd->getName();
            }
            $replace['#cmd_' . $cmd->getLogicalId() . '_unite#'] = $cmd->getUnite();
            $replace['#cmd_' . $cmd->getLogicalId() . '_collectDate#'] = $cmd->getCollectDate();
            $replace['#cmd_' . $cmd->getLogicalId() . '_valueDate#'] = $cmd->getValueDate();
        }
        $replace['#cmd#'] = $cmd_html;

        foreach ($this->getCmd('action', null) as $cmdAction) {
            if ($cmdAction->getConfiguration('ref', false)) {
                $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_ref#'] = str_replace('\'', ' ',json_encode($cmdAction->getConfiguration('ref')));
            }
            if ($cmdAction->getConfiguration('dataSetList', false)) {
                $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_dataSetList#'] = str_replace('\'', ' ',json_encode($cmdAction->getConfiguration('dataSetList')));
            }

            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_id#'] = $cmdAction->getId();
            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_logicalid#'] = $cmdAction->getLogicalId();
            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_name#'] = ($cmdAction->getDisplay('icon') != '') ? $cmdAction->getDisplay('icon') : $cmdAction->getName();
            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_unite#'] = $cmdAction->getUnite();
            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_collectDate#'] = $cmdAction->getCollectDate();
            $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_valueDate#'] = $cmdAction->getValueDate();
            if ($cmdAction->getDisplay('showIconAndName' . $_version, 0) == 1) {
                $replace['#cmdAction_' . $cmdAction->getLogicalId() . '_name#'] = $cmdAction->getDisplay('icon') . ' ' . $cmdAction->getName();
            }
        }

        $html = template_replace($replace, getTemplate('core', $_version, 'lgthinq2.' . $deviceType . '.template',__CLASS__));
        $html = translate::exec($html, 'plugins/lgthinq2/core/template/' . $version . '/lgthinq2.' . $deviceType . '.template.html');
        return $html;
    }
}

class lgthinq2Cmd extends cmd
{
    public static $_widgetPossibility = array('custom' => true);

    /**
     * Exécute une action sur l'équipement associé à cette commande.
     *
     * Cette fonction exécute une action sur l'équipement associé à cette commande en fonction des options fournies ($_options).
     * Elle vérifie d'abord le type de sous-commande (subtype) pour déterminer le type d'action à effectuer.
     * Ensuite, elle traite les différentes actions possibles, telles que la mise à jour de la valeur d'un curseur (slider), d'une couleur (color), d'une sélection (select) ou l'affichage d'un message (message).
     * La fonction prend également en charge l'envoi de données à l'équipement en fonction du type de plateforme (thinq1 ou thinq2) et du type de contrôle (BINARY(BYTE) ou autre).
     * Elle gère les réponses et les erreurs renvoyées par l'équipement et met à jour les informations de la commande en conséquence.
     *
     * @param array $_options Les options à utiliser pour l'exécution de l'action sur l'équipement.
     * @return bool Retourne true si l'action a été exécutée avec succès, sinon false.
     */
    public function execute($_options = array())
    {
        $eqLogic = $this->getEqLogic();
        log::add('lgthinq2', 'debug', __("Action sur ", __FILE__) . $this->getLogicalId() . __(" avec options ", __FILE__) . json_encode($_options));

        if ($this->getLogicalId() == 'refresh') {
            $eqLogic->refresh();
            return;
        }
        $resValue = '';
        $replace = array();
        $configurationValue = $this->getConfiguration('value', '');

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
        $result = [];
        if ($configurationValue != '') {
            $result = [];
            if (preg_match_all('/#(.*?)#/', $configurationValue, $matches)) {
            log::add('lgthinq2', 'debug', __("MATCHESSSS ", __FILE__) . json_encode($matches));
                foreach ($matches[0] as $value) {
                    $objCmd = null;
                    if ($value !== '#select#') {
                        $objCmd = $eqLogic->getCmd('info', str_replace('#','',$value));
                        if (is_object($objCmd)) {
                            $valMap = $objCmd->getConfiguration('valueMapping', '');
                            if ($valMap != '') {
                                $replace[$value] = intval(array_search($objCmd->execCmd(), $valMap));
                            }
                        }
                    }
                }
            }
        }
        $value = str_replace(array_keys($replace),$replace,$this->getConfiguration('updateLGCmdToValue', ''));
        $keyValue = str_replace(array_keys($replace),$replace,$configurationValue);
        if (lgthinq2::isValidJson($keyValue)) {
            $keyValue = json_decode($keyValue, true);
        }

        lgthinq2::getTokenIsExpired();

        if ($eqLogic->getConfiguration('platformType') == 'thinq1') {
            $headers = lgthinq2::defaultDevicesEmpHeaders();
            $headers[] = 'x-thinq-jsessionId: ' . config::byKey('jsessionId', 'lgthinq2', lgthinq2::step6());
            log::add('lgthinq2', 'debug', __("Données à envoyer en thinq1 headers ", __FILE__) . json_encode($headers));

            if ($this->getConfiguration('controlType') == 'BINARY(BYTE)') {
                $paramDataList = $eqLogic->getConfiguration('paramDataList');
                $monitoring = $eqLogic->getConfiguration('Monitoring');
                $arrayData = array();
                $i = 0;
                foreach ($paramDataList as $keyToSend) {
                    if (!is_numeric($keyToSend)) {
                        if ($keyToSend == $this->getConfiguration('paramKey')) {
                            $arrayData[] = intval($value);
                        } else {
                            $objCmd = $eqLogic->getCmd('info', $keyToSend);
                            if (is_object($objCmd)) {
                                $valMap = $objCmd->getConfiguration('valueMapping', '');
                                if ($valMap != '') {
                                    $arrayData[] = intval(array_search($objCmd->execCmd(), $valMap));
                                }
                            } else {
                                $arrayData[] = 255;
                            }
                        }
                    } else {
                        $arrayData[] = intval($keyToSend)??255;
                    }
                }

                $data = array(
                    lgthinq2::DATA_ROOT => array(
                        'cmd'      => $this->getConfiguration('cmd'),
                        'cmdOpt'   => $this->getConfiguration('cmdOpt'),
                        'data'     => lgthinq2::encodeMonitorBinary($arrayData),
                        'deviceId' => $eqLogic->getLogicalId(),
                        'format'   => 'B64',
                        'value'    => $keyValue,
                        'workId'   => lgthinq2::setUUID()
                    )
                );
                $data = json_encode($data, JSON_UNESCAPED_SLASHES);
            } else {
                $data = array(
                    lgthinq2::DATA_ROOT => array(
                        'cmd'      => $this->getConfiguration('cmd'),
                        'cmdOpt'   => $this->getConfiguration('cmdOpt'),
                        'deviceId' => $eqLogic->getLogicalId(),
                        'value'    => ($keyValue!=''?$keyValue:$value),
                        'workId'   => lgthinq2::setUUID(),
                        'data'     => ''
                    )
                );
                $data = json_encode($data, JSON_PRETTY_PRINT);
            }
            log::add('lgthinq2', 'debug', __("Données à envoyer en thinq1 ", __FILE__) . $data);

            $response = lgthinq2::postData(lgthinq2::LGTHINQ1_SERV_DEVICES . 'rti/rtiControl', $data, $headers);
            log::add('lgthinq2', 'debug', __FUNCTION__ . ' : Réponse récupérée ' . $response);
            if ($response) {
                $arr = json_decode($response, true);
                if (!$arr || !isset($arr[lgthinq2::DATA_ROOT])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($arr));
                    return;
                }
                if (!isset($arr[lgthinq2::DATA_ROOT]['returnCd'])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Erreur de la réponse ', __FILE__) . json_encode($arr[lgthinq2::DATA_ROOT]));
                    return;
                }
                if ($arr[lgthinq2::DATA_ROOT]['returnCd'] != '0000') {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Code retour erroné ', __FILE__) . json_encode($arr[lgthinq2::DATA_ROOT]));
                    return;
                }
                if (isset($arr[lgthinq2::DATA_ROOT]['returnData'])) {
                    $returnUndecodedData = $arr[lgthinq2::DATA_ROOT]['returnData'];
                    log::add(__CLASS__, 'debug', __FUNCTION__ . ' ' . __('Requête réussie ', __FILE__) . json_encode($returnUndecodedData));
                    if ($arr[lgthinq2::DATA_ROOT]['format'] == 'B64') {
                        if ($eqLogic->getConfiguration('MonitoringType') == 'JSON') {
                            $resValue = json_decode(base64_decode($returnUndecodedData), true);
                        } else if ($eqLogic->getConfiguration('MonitoringType') == 'BINARY(BYTE)') {
                            $resValue = lgthinq2::decodeMonitorBinary($returnUndecodedData, $eqLogic->getConfiguration('Monitoring'));
                        }
                    } else {
                        $resValue = $returnUndecodedData; // else put it on string info cmd
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
                'dataValue' => $value != '' ? $value : $this->getConfiguration('dataValue', $value)
            );

            if ($this->getConfiguration('dataSetList', '') == '') {
                $refState = lgthinq2::deviceTypeConstantsState($eqLogic->getConfiguration('deviceType')); // to get "resState" keytree
                if ($refState && $value != '') {
                    $data['dataSetList'] = array(
                        $refState => array(
                           str_replace($this->getConfiguration('cmd'), '', $this->getLogicalId()) => $value
                        )
                    );
                }
            } else {
                if ($_options['course'] != '') {
                    $refState = lgthinq2::deviceTypeConstantsState($eqLogic->getConfiguration('deviceType')); // to get "resState" keytree
                    $data['dataSetList'] = array(
                        $refState => $_options['course']
                    );
                } else {
                    $data['dataSetList'] = $this->getConfiguration('dataSetList');
                }
            }

            log::add('lgthinq2', 'debug', __("Donnée envoyée en thinq2 ", __FILE__) . json_encode($data));
            $response = lgthinq2::postData(lgthinq2::LGTHINQ2_SERV_URL . 'service/devices/' . $eqLogic->getLogicalId() . '/control-sync', json_encode($data, JSON_NUMERIC_CHECK), $headers);
            log::add('lgthinq2', 'debug', __("Réponse reçue en thinq2 ", __FILE__) . $response);
            if ($response) {
                $arr = json_decode($response, true);
                if (!$arr || !isset($arr['resultCode'])) {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Erreur de la requête ', __FILE__) . json_encode($arr));
                    return;
                }
                if ($arr['resultCode'] != '0000') {
                    log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Erreur de code ', __FILE__) . json_encode($arr));
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
                        $value = str_replace('#slider#', $_options['slider'], $value);
                        break;
                    case 'color':
                        $value = str_replace('#color#', $_options['color'], $value);
                        break;
                    case 'select':
                        $value = str_replace('#select#', $_options['select'], $value);
                        if ($cmd->getSubType() == 'binary') {
                            if ($cmd->getConfiguration('valueMapping', '')) {
                                if (isset($cmd->getConfiguration('valueMapping')[$value])) {
                                    $value = $cmd->getConfiguration('valueMapping')[$value];
                                }
                            }
                        }
                        break;
                    case 'message':
                        $value = str_replace('#message#', $_options['message'], $value);
                        break;
                    case 'other':
                        $value = $resValue;
                        break;
                }

                log::add('lgthinq2', 'debug', __FUNCTION__ . ' ' . __('Réponse décodée ', __FILE__) . $value . __(' transmise dans ', __FILE__) . $cmd->getName());
                $eqLogic->checkValueAndUpdateCmd($cmd->getLogicalId(), $value, null);

                //$cmd->event($value);
            }
        }
        return true;
    }

    /**
     * Convertit une valeur de chaîne en binaire.
     *
     * Cette fonction prend en entrée une valeur de chaîne ($_value) et la convertit en une valeur binaire (0 ou 1) en fonction de la correspondance avec des valeurs spécifiques.
     * Elle prend en charge plusieurs valeurs prédéfinies et des correspondances de chaînes régulières pour convertir les valeurs en binaire.
     * La fonction renvoie la valeur binaire résultante après conversion.
     *
     * @param string $_value La valeur de chaîne à convertir en binaire.
     * @return int La valeur binaire résultante (0 ou 1) après conversion.
     */
    public static function getStringToBinaryValues($_value)
    {
        $_value = trim($_value);
        if ($_value === true || $_value === 'true') {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour 1 : ', __FILE__) . $_value);
            return 1;
        } elseif (preg_match("/\_OFF($|_)/", $_value)) {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour OFF : ', __FILE__) . $_value);
            return 0;
        } elseif (preg_match('/\_ON($|_)/', $_value)) {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour ON : ', __FILE__) . $_value);
            return 1;
        } elseif (in_array($_value, ['1', 'true', '@C', '@WATER', 'OK', 'OPEN', 'LOCK', '\u2103', 'ON', 'Activ\u00e9', 'Activé', '℃'])) {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour 1 : ', __FILE__) . $_value);
            return 1;
        } elseif (in_array($_value, ['0', 0, false, 'false', '@F', '@NON', '@FAIL', '@AIR', 'FAIL', 'CLOSE', 'UNLOCK', '\uff26', 'OFF', 'D\u00e9sactiv\u00e9', 'Désactivé', '℉'])) {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour 0 : ', __FILE__) . $_value);
            return 0;
        } else {
            log::add('lgthinq2', 'info', __FUNCTION__ . ' ' . __('commande mise à jour else : ', __FILE__) . $_value);
            return $_value;
        }
    }

    /**
     * Récupère les valeurs possibles pour une valeur binaire.
     *
     * @param int $_binary La valeur binaire (0 ou 1)
     * @return array Les valeurs possibles correspondantes
     */
    public static function getBinaryToStringValues($_binary)
    {
        $_binary = intval($_binary);

        if ($_binary === 1) {
            return [
                '_ON',
                '_ON_',
                '1',
                'true',
                '@C',
                '@WATER',
                'OK',
                'OPEN',
                'LOCK',
                '\u2103',
                'ON',
                'Activ\u00e9',
                'Activé',
                '℃'
            ];
        }

        if ($_binary === 0) {
            return [
                '_OFF',
                '_OFF_',
                '0',
                'false',
                '@F',
                '@NON',
                '@FAIL',
                '@AIR',
                'FAIL',
                'CLOSE',
                'UNLOCK',
                '\uff26',
                'OFF',
                'D\u00e9sactiv\u00e9',
                'Désactivé',
                '℉'
            ];
        }

        return [];
    }


	public function toHtml($_version = 'dashboard', $_options = '') {
		$_version = jeedom::versionAlias($_version);
		$html = '';
		$replace = array(
			'#id#' => $this->getId(),
			'#name#' => $this->getName(),
			'#name_display#' => ($this->getDisplay('icon') != '') ? $this->getDisplay('icon') : $this->getName(),
			'#history#' => '',
			'#hide_history#' => 'hidden',
			'#unite#' => $this->getUnite(),
			'#raw_unite#' => $this->getUnite(),
			'#minValue#' => $this->getConfiguration('minValue', 0),
			'#maxValue#' => $this->getConfiguration('maxValue', 100),
			'#logicalId#' => $this->getLogicalId(),
			'#uid#' => 'cmd' . $this->getId() . eqLogic::UIDDELIMITER . mt_rand() . eqLogic::UIDDELIMITER,
			'#version#' => $_version,
			'#eqLogic_id#' => $this->getEqLogic_id(),
			'#generic_type#' => $this->getGeneric_type(),
			'#hide_name#' => '',
			'#value_history#' => ''
		);
		if ($this->getConfiguration('listValue', '') != '') {
			$listOption = '';
			$elements = explode(';', $this->getConfiguration('listValue', ''));
			$foundSelect = false;
			foreach ($elements as $element) {
				$coupleArray = explode('|', $element);
				$cmdValue = $this->getCmdValue();
				if (is_object($cmdValue) && $cmdValue->getType() == 'info') {
                    $keySelected = intval($this->getConfiguration('listValueSelected', 0));
                    $valueCmdValue = $cmdValue->execCmd();
                    if ($cmdValue->getSubType() == 'binary') {
                        $valueMap = $cmdValue->getConfiguration('valueMapping', array());
                        $_arrayValue = lgthinq2Cmd::getBinaryToStringValues($valueCmdValue);
                        if (in_array($valueCmdValue, $_arrayValue)) {
                            foreach ($valueMap as $keyV => $valV) {
                                if (in_array($valV,$_arrayValue)) {
                                    $valueCmdValue = $keyV;
                                }
                            }
                        }
                    }
					if ($valueCmdValue == $coupleArray[$keySelected]) {
						$listOption .= '<option value="' . $coupleArray[0] . '" selected>' . $coupleArray[1] . '</option>';
						$foundSelect = true;
					} else {
						$listOption .= '<option value="' . $coupleArray[0] . '">' . $coupleArray[1] . '</option>';
					}
				} else {
					if (isset($coupleArray[1])) {
						$listOption .= '<option value="' . $coupleArray[0] . '">' . $coupleArray[1] . '</option>';
					} else {
						$listOption .= '<option value="' . $coupleArray[0] . '">' . $coupleArray[0] . '</option>';
					}
				}
			}
			if (!$foundSelect) {
				$listOption = '<option value="">Aucun</option>' . $listOption;
			}
			$replace['#listValue#'] = $listOption;
		}
		if ($this->getDisplay('showNameOn' . $_version, 1) == 0) {
			$replace['#hide_name#'] = 'hidden';
		}
		if ($this->getDisplay('showIconAndName' . $_version, 0) == 1) {
			$replace['#name_display#'] = $this->getDisplay('icon') . ' ' . $this->getName();
		}
		$widget = $this->getWidgetTemplateCode($_version);
		$template = $widget['template'];
		$isCorewidget = $widget['isCoreWidget'];
		if ($_version == 'scenario' && $isCorewidget) {
			$widget['widgetName'] = 'cmd.' . $this->getType() . '.' . $this->getSubType() . '.default';
		}

		if ($_options != '') {
			$options = jeedom::toHumanReadable($_options);
			$options = is_json($options, $options);
			if (is_array($options)) {
				foreach ($options as $key => $value) {
					$replace['#' . $key . '#'] = $value;
				}
			}
		}
		if ($this->getType() == 'info') {
			$replace['#value#'] = '';
			$replace['#tendance#'] = '';
			$replace['#value#'] = $this->execCmd();
			if ($this->getSubType() == 'binary' && $this->getDisplay('invertBinary') == 1) {
				$replace['#value#'] = ($replace['#value#'] == 1) ? 0 : 1;
			}
			if ($this->getSubType() == 'numeric' && trim($replace['#value#']) === '') {
				$replace['#value#'] = 0;
			}
			if ($this->getSubType() == 'numeric' && trim($replace['#unite#']) != '') {
				if ($this->getConfiguration('historizeRound') !== '' && is_numeric($this->getConfiguration('historizeRound')) && $this->getConfiguration('historizeRound') >= 0) {
					$round = $this->getConfiguration('historizeRound');
				} else {
					$round = 99;
				}
				$valueInfo = self::autoValueArray($replace['#value#'], $round, $replace['#unite#']);
				$replace['#state#'] = $valueInfo[0];
				$replace['#unite#'] = $valueInfo[1];
			}
			if (!isset($replace['#state#'])) {
				$replace['#state#'] = $replace['#value#'];
			}
			if ($this->getSubType() == 'string') {
				$replace['#value#'] = str_replace("\n", '<br/>', addslashes($replace['#value#']));
			}
			if (method_exists($this, 'formatValueWidget')) {
				$replace['#state#'] = $this->formatValueWidget($replace['#state#']);
			}

			$replace['#state#'] = str_replace(array("\'", "'", "\n"), array("'", "\'", '<br/>'), $replace['#state#']);
			$replace['#collectDate#'] = $this->getCollectDate();
			$replace['#valueDate#'] = $this->getValueDate();
			$replace['#alertLevel#'] = $this->getCache('alertLevel', 'none');
			if ($this->getIsHistorized() == 1) {
				$replace['#history#'] = 'history cursor';
				if (config::byKey('displayStatsWidget') == 1 && strpos($template, '#hide_history#') !== false && $this->getDisplay('showStatsOn' . $_version, 1) == 1) {
					$startHist = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . config::byKey('historyCalculPeriod') . ' hour'));
					$replace['#hide_history#'] = '';
					$historyStatistique = $this->getStatistique($startHist, date('Y-m-d H:i:s'));
					if ($historyStatistique['avg'] == 0 && $historyStatistique['min'] == 0 && $historyStatistique['max'] == 0) {
						$replace['#averageHistoryValue#'] = round(intval($replace['#state#']), 1);
						$replace['#minHistoryValue#'] = round(intval($replace['#state#']), 1);
						$replace['#maxHistoryValue#'] = round(intval($replace['#state#']), 1);
					} else {
						$replace['#averageHistoryValue#'] = round(intval($historyStatistique['avg']), 1);
						$replace['#minHistoryValue#'] = round(intval($historyStatistique['min']), 1);
						$replace['#maxHistoryValue#'] = round(intval($historyStatistique['max']), 1);
					}
					$startHist = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . config::byKey('historyCalculTendance') . ' hour'));
					$tendance = $this->getTendance($startHist, date('Y-m-d H:i:s'));
					if ($tendance > config::byKey('historyCalculTendanceThresholddMax')) {
						$replace['#tendance#'] = 'fas fa-arrow-up';
					} else if ($tendance < config::byKey('historyCalculTendanceThresholddMin')) {
						$replace['#tendance#'] = 'fas fa-arrow-down';
					} else {
						$replace['#tendance#'] = 'fas fa-minus';
					}
				}
			}
			$parameters = $this->getDisplay('parameters');
			if (is_array($parameters)) {
				foreach ($parameters as $key => $value) {
					$replace['#' . $key . '#'] = $value;
				}
			}
		}

		if ($this->getType() == 'action') {
			$cmdValue = $this->getCmdValue();
			if (is_object($cmdValue) && $cmdValue->getType() == 'info') {
				$replace['#value_id#'] = $cmdValue->getId();
				$replace['#state#'] = $cmdValue->execCmd();
				$replace['#valueName#'] = $cmdValue->getName();
				$replace['#unite#'] = $cmdValue->getUnite();
				$replace['#collectDate#'] = $cmdValue->getCollectDate();
				$replace['#valueDate#'] = $cmdValue->getValueDate();
				$replace['#value_history#'] = ($cmdValue->getIsHistorized() == 1) ? 'history cursor' : '';
				$replace['#alertLevel#'] = $cmdValue->getCache('alertLevel', 'none');
				if (trim($replace['#state#']) === '' && ($cmdValue->getSubtype() == 'binary' || $cmdValue->getSubtype() == 'numeric')) {
					$replace['#state#'] = 0;
				}
				if ($cmdValue->getSubType() == 'binary' && $cmdValue->getDisplay('invertBinary') == 1) {
					$replace['#state#'] = ($replace['#state#'] == 1) ? 0 : 1;
				}
			} else {
				$replace['#state#'] = ($this->getLastValue() !== null) ? $this->getLastValue() : '';
				$replace['#valueName#'] = $this->getName();
				$replace['#unite#'] = $this->getUnite();
			}
			$replace['#state#'] = str_replace(array("\'", "'"), array("'", "\'"), $replace['#state#']);

			$html .= template_replace($replace, $template);
			if (trim($html) == '') {
				return $html;
			}

			$replace['#title_placeholder#'] = $this->getDisplay('title_placeholder', __('Titre', __FILE__));
			$replace['#message_placeholder#'] = $this->getDisplay('message_placeholder', __('Message', __FILE__));
			$replace['#message_cmd_type#'] = $this->getDisplay('message_cmd_type', 'info');
			$replace['#message_cmd_subtype#'] = $this->getDisplay('message_cmd_subtype', '');
			$replace['#message_disable#'] = $this->getDisplay('message_disable', 0);
			$replace['#title_disable#'] = $this->getDisplay('title_disable', 0);
			$replace['#title_color#'] = $this->getDisplay('title_color', 0);
			$replace['#title_possibility_list#'] = str_replace("'", "\'", $this->getDisplay('title_possibility_list', ''));
			$replace['#slider_placeholder#'] = $this->getDisplay('slider_placeholder', __('Valeur', __FILE__));
			$replace['#other_tooltips#'] = ($replace['#name#'] != $this->getName()) ? $this->getName() : '';

			$parameters = $this->getDisplay('parameters');
			if (is_array($parameters)) {
				foreach ($parameters as $key => $value) {
					$replace['#' . $key . '#'] = $value;
				}
			}

			if (!isset($replace['#title#'])) {
				$replace['#title#'] = '';
			} else {
				$replace['#title#'] = htmlspecialchars($replace['#title#']);
			}
			if (!isset($replace['#message#'])) {
				$replace['#message#'] = '';
			}
			if (!isset($replace['#slider#'])) {
				$replace['#slider#'] = '';
			}
			if (!isset($replace['#color#'])) {
				$replace['#color#'] = '';
			}
		}

		$template = template_replace($replace, $template);
		if ($isCorewidget && $_version == 'scenario') {
			return translate::exec($template, 'core/template/scenario/' . $widget['widgetName'] . '.html');
		}
		if ($isCorewidget) {
			return translate::exec($template, 'core/template/widgets.html');
		}
		if (isset($widget['widgetName'])) {
			return translate::exec($template, $widget['widgetName']);
		}
		return $template;
	}

    /**
     * Génère le code HTML pour l'affichage de la commande.
     *
     * Cette fonction génère le code HTML pour l'affichage de la commande selon la version spécifiée.
     *
     * @param string $_version La version de l'affichage (par défaut : 'dashboard').
     * @return string Le code HTML généré pour l'affichage de l'équipement.
     */
    public function formatValueWidget($_value)
    {
        if ($this->getSubType() != 'binary') {
            $valueMap = $this->getConfiguration('valueMapping', '');
            if ($valueMap != '') {
                if (isset($valueMap[$_value])) {
                    if (isset($valueMap[$_value]['label']) && $valueMap[$_value]['label'] != '') {
                        return $valueMap[$_value]['label'];
                    } elseif (isset($valueMap[$_value]['title']) && $valueMap[$_value]['title'] != '') {
                        return $valueMap[$_value]['title'];
                    } elseif (isset($valueMap[$_value]['content']) && $valueMap[$_value]['content'] != '') {
                        return $valueMap[$_value]['content'];
                    } elseif (isset($valueMap[$_value]['comment']) && $valueMap[$_value]['comment'] != '') {
                        return $valueMap[$_value]['comment'];
                    } elseif (isset($valueMap[$_value]['index']) && $valueMap[$_value]['index'] != '') {
                        return $valueMap[$_value]['index'];
                    }
                }
            }
        }
        return $_value;
    }
}
