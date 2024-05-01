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

class lgthinq2_customLang
{
    public $customlang;

    public function __construct()
    {
        $this->customlang = [
            "@AC_MAIN_OPERATION_RIGHT_ON_W"           => __("Ventilateur de droite uniquement", __FILE__),
            "@AC_MAIN_OPERATION_LEFT_ON_W"            => __("Ventilateur de gauche uniquement", __FILE__),
            "@AC_MAIN_OPERATION_ALL_ON_W"             => __("Tous les ventilateurs", __FILE__),
            "@AC_MAIN_OPERATION_MODE_AROMA_W"         => __("Aroma", __FILE__),
            "@AC_MAIN_AROMA_OFF_W"                    => __("Désactivé", __FILE__),
            "@AC_MAIN_AROMA_ON_W"                     => __("Activé", __FILE__),
            "@DARK"                                   => __("Sombre", __FILE__),
            "@MID"                                    => __("Intermédiaire", __FILE__),
            "@BRIGHT"                                 => __("Lumineux", __FILE__),
            "@MIN"                                    => __("Minumum", __FILE__),
            "@MAX"                                    => __("Maximum", __FILE__),
            "@1"                                      => "1",
            "@2"                                      => "2",
            "@3"                                      => "3",
            "@4"                                      => "4",
            "@5"                                      => "5",
            "@6"                                      => "6",
            "@7"                                      => "7",
            "@8"                                      => "8",
            "@9"                                      => "9",
            "@10"                                     => "10",
            "@AC_MAIN_OPERATION_MODE_COOL_W"          => __("Refroidissement", __FILE__),
            "@AC_MAIN_OPERATION_MODE_DRY_W"           => __("Opération automatique", __FILE__),
            "@AC_MAIN_OPERATION_MODE_FAN_W"           => __("Ventilation", __FILE__),
            "@AC_MAIN_OPERATION_MODE_AI_W"            => __("Ventilation", __FILE__),
            "@AC_MAIN_OPERATION_MODE_HEAT_W"          => __("Chauffage", __FILE__),
            "@AC_MAIN_OPERATION_MODE_AIRCLEAN_W"      => "AC_MAIN_OPERATION_MODE_AIRCLEAN_W", //à traduire
            "@AC_MAIN_OPERATION_MODE_ACO_W"           => "AC_MAIN_OPERATION_MODE_ACO_W", //à traduire
            "@AC_MAIN_OPERATION_MODE_AROMA_W"         => __("Aroma", __FILE__),
            "@AC_MAIN_OPERATION_MODE_ENERGY_SAVING_W" => __("Économie d'énergie", __FILE__),
            "@AP_MAIN_MID_OPMODE_CLEAN_W"             => __("Nettoyage", __FILE__),
            "@AP_MAIN_MID_OPMODE_SLEEP_W"             => __("Veille", __FILE__),
            "@AP_MAIN_MID_OPMODE_SILENT_W"            => __("Silencieux", __FILE__),
            "@AP_MAIN_MID_OPMODE_HUMIDITY_W"          => __("Humidité", __FILE__),
            "@AP_MAIN_MID_OPMODE_CIRCULATOR_CLEAN_W"  => __("Nettoyage pompe", __FILE__),
            "@AP_MAIN_MID_OPMODE_BABY_CARE_W"         => __("Linge de bébé", __FILE__),
            "@AP_MAIN_MID_OPMODE_DUAL_CLEAN_W"        => "AP_MAIN_MID_OPMODE_DUAL_CLEAN_W", //à traduire
            "@AP_MAIN_MID_OPMODE_AUTO_W"              => __("Automatique", __FILE__),
            "@AP_MAIN_MID_OPMODE_SMART_DEHUM_W"       => __("Déshumidification intelligente", __FILE__),
            "@AP_MAIN_MID_OPMODE_FAST_DEHUM_W"        => __("Déshumidification rapide", __FILE__),
            "@AP_MAIN_MID_OPMODE_CILENT_DEHUM_W"      => __("Déshumidification silencieuse ", __FILE__),
            "@AP_MAIN_MID_OPMODE_CONCENTRATION_DRY_W" => "AP_MAIN_MID_OPMODE_CONCENTRATION_DRY_W", //à traduire
            "@AP_MAIN_MID_OPMODE_CLOTHING_DRY_W"      => __("Séchage des vêtements", __FILE__),
            "@AP_MAIN_MID_OPMODE_IONIZER_W"           => __("Ioniseur", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_SLOW_W"           => __("Lent", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_SLOW_LOW_W"       => __("Très lent", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_W"            => __("Bas", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_MID_W"        => __("Moyen-bas", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_W"            => __("Moyen", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_HIGH_W"       => __("Moyen-haut", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_W"           => __("Haut", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_POWER_W"          => __("Puissant", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_AUTO_W"           => __("Automatique", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_NATURE_W"         => __("Vent naturel", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_RIGHT_W"      => __("Bas-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_RIGHT_W"      => __("Moyen-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_RIGHT_W"     => __("Haut-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_LEFT_W"       => __("Bas-gauche", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_LEFT_W"       => __("Moyen-gauche", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_LEFT_W"      => __("Haut-gauche", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_LEFT_W|AC_MAIN_WIND_STRENGTH_LOW_RIGHT_W"     => __("Bas-gauche bas-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_LEFT_W|AC_MAIN_WIND_STRENGTH_MID_RIGHT_W"     => __("Bas-gauche moyen-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_LOW_LEFT_W|AC_MAIN_WIND_STRENGTH_HIGH_RIGHT_W"    => __("Bas-gauche haut-droite", __FILE__),    
            "@AC_MAIN_WIND_STRENGTH_MID_LEFT_W|AC_MAIN_WIND_STRENGTH_LOW_RIGHT_W"     => __("Moyen-gauche bas-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_LEFT_W|AC_MAIN_WIND_STRENGTH_MID_RIGHT_W"     => __("Moyen-gauche moyen-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_MID_LEFT_W|AC_MAIN_WIND_STRENGTH_HIGH_RIGHT_W"    => __("Moyen-gauche haut-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_LEFT_W|AC_MAIN_WIND_STRENGTH_LOW_RIGHT_W"    => __("Haut-gauche bas-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_LEFT_W|AC_MAIN_WIND_STRENGTH_MID_RIGHT_W"    => __("Haut-gauche moyen-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_HIGH_LEFT_W|AC_MAIN_WIND_STRENGTH_HIGH_RIGHT_W"   => __("Haut-gauche haut-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_AUTO_LEFT_W|AC_MAIN_WIND_STRENGTH_AUTO_RIGHT_W"   => __("Automatique-gauche automatique-droite", __FILE__),
            "@AC_MAIN_WIND_STRENGTH_POWER_LEFT_W|AC_MAIN_WIND_STRENGTH_POWER_RIGHT_W" => __("Puissant-gauche puissant-droite", __FILE__),
        ];
    }
}
?>