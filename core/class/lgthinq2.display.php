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
require_once __DIR__ . '/../../../../plugins/lgthinq2/core/class/lgthinq2.class.php';

class lgthinq2_display extends eqLogic
{
    public static function displayActionCard($action_name, $fa_icon, $attr = '', $class = '') {
        $actionCard = '<div class="eqLogicDisplayAction eqLogicAction cursor ' . $class . '" ';
        if ($attr != '') $actionCard .= $attr;
        $actionCard .= '>';
        $actionCard .= '    <i class="fas ' . $fa_icon . '"></i><br>';
        $actionCard .= '    <span>' . $action_name . '</span>';
        $actionCard .= '</div>';
        echo $actionCard;
    }

    public static function displayBtnAction($class, $action, $title, $logo, $text, $display = FALSE) {
        $btn = '<a class="eqLogicAction btn btn-sm ' . $class . '"';
        $btn .= '    data-action="' . $action . '"';
        $btn .= '    title="' . $title . '"';
        if ($display) $btn .= '    style="display:none"';
        $btn .= '>';
        $btn .= '  <i class="fas ' . $logo . '"></i> ';
        $btn .= $text;
        $btn .= '</a>';
        echo $btn;
    }

    public static function displayEqLogicThumbnailContainer($eqLogics, $_type) {
        echo '<div class="panel panel-default">';
        echo '    <h3 class="panel-title">';
        echo '        <a id="accordionlgthinq2" class="accordion-toggle" data-toggle="collapse" data-parent="" href="#lgthinq2_' . $_type . '"><i class="' . lgthinq2::getIconClass($_type) . '"></i> ' . lgthinq2::getDeviceLabel($_type) . '</a>';
        echo '    </h3>';
        echo '    <div id="lgthinq2_' . $_type . '" class="panel-collapse collapse in">';
        echo '        <div class="eqLogicThumbnailContainer">';
        foreach ($eqLogics as $eqLogic) {
            $additionalInfo = ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Équipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Équipement non visible}}"></i>';

            $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
            echo '            <div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
            echo '                <img src="' . $eqLogic->getImage() . '"/>';
            echo '                <br>';
            echo '                <span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
            echo '                <span class="hidden hiddenAsCard displayTableRight">' . $additionalInfo . '</span>';
            echo '            </div>';
        }
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
  
    public static function displayPushMessages($_type, $_count) {
        echo '<div class="panel panel-default">';
        echo '    <h3 class="panel-title">';
        echo '        <a id="accordionMessageslgthinq2" class="accordion-toggle" data-toggle="collapse" data-parent="" href="#lgthinq2_' . $_type . '">' . lgthinq2::getMessagesTypeLabel($_type) . ' ('.$_count.' messages)</a>';
        echo '    </h3>';
        echo '    <div id="lgthinq2_' . $_type . '" class="panel-collapse collapse in">';
        echo '        <div class="pushMessageContent">';
        echo '            <table class="table table-condensed table-bordered tablesorter" id="table_messageslgthinq2">';
        echo '			      <thead>';
        echo '				      <tr>';
        echo '					      <th data-sort="int"><span class="scanHender"><b class="caret"></b> {{ID}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Corps}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="int"><span class="scanHender"><b class="caret"></b> {{Date d\'événement}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="int"><span class="scanHender"><b class="caret"></b> {{Date d\'expiration}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Message}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Actions}} <b class="caret"></b></span></th>';
        echo '				      </tr>';
        echo '			      </thead>';
        echo '			      <tbody>';
        echo '			      </tbody>';
        echo '            </table>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
  
    public static function displayFirmware($_type, $_count) {
        echo '<div class="panel panel-default">';
        echo '    <h3 class="panel-title">';
        echo '        <a id="accordionFirmwarelgthinq2" class="accordion-toggle" data-toggle="collapse" data-parent="" href="#lgthinq2_Firmware">' . lgthinq2::getMessagesTypeLabel($_type) . ' ('.$_count.' appareils)</a>';
        echo '    </h3>';
        echo '    <div id="lgthinq2_Firmware" class="panel-collapse collapse in">';
        echo '        <div class="firmwareContent">';
        echo '            <table class="table table-condensed table-bordered tablesorter" id="table_firmwarelgthinq2">';
        echo '			      <thead>';
        echo '				      <tr>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Image}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="int"><span class="scanHender"><b class="caret"></b> {{UUID}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Code}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string" colspan="2"><span class="scanHender"><b class="caret"></b> {{Infos}} <b class="caret"></b></span></th>';
        echo '					      <th data-sort="string"><span class="scanHender"><b class="caret"></b> {{Actions}} <b class="caret"></b></span></th>';
        echo '				      </tr>';
        echo '			      </thead>';
        echo '			      <tbody>';
        echo '			      </tbody>';
        echo '            </table>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
}