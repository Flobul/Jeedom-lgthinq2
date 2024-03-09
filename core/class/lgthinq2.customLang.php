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
            "@AC_MAIN_OPERATION_RIGHT_ON_W"   => __("Ventilateur de droite uniquement", __FILE__),
            "@AC_MAIN_OPERATION_LEFT_ON_W"    => __("Ventilateur de gauche uniquement", __FILE__),
            "@AC_MAIN_OPERATION_ALL_ON_W"     => __("Tous les ventilateurs", __FILE__),
            "@AC_MAIN_OPERATION_MODE_AROMA_W" => __("Aroma", __FILE__),
            "@AC_MAIN_AROMA_OFF_W"            => __("Désactivé", __FILE__),
            "@AC_MAIN_AROMA_ON_W"             => __("Activé", __FILE__),
            "@DARK"                           => __("Sombre", __FILE__),
            "@MID"                            => __("Intermédiaire", __FILE__),
            "@BRIGHT"                         => __("Lumineux", __FILE__),
            "@MIN"                            => __("Minumum", __FILE__),
            "@MAX"                            => __("Maximum", __FILE__),
        ];
    }
}
?>
