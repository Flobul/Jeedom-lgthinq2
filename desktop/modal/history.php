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
require_once __DIR__ . '/../../../../plugins/vesync/core/class/vesync.display.php';

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<style>
    .scanHender{
        cursor: pointer !important;
        width: 100%;
    }
    .changeActivate i.fas.fa-toggle-on {
        color: green !important;
    }
    .changeActivate i.fas.fa-toggle-off {
        color: red !important;
    }
</style>
    <span class='pull-right'>
        <a class="btn btn-default pull-right" id="bt_refreshPushmessages"><i class="fas fa-sync-alt"></i> {{Rafraîchir}}</a>
    </span>

      <?php
        $result = lgthinq2::getUsersNotifications();
        if (is_array($result) && isset($result['result'])) {
            sendVarToJs('pushMessages', $result['result']);
           
        }
      ?>

<script>
  $('#bt_refreshPushmessages').on('click', function() {
    $('#md_modal').dialog('close');
    $('#md_modal').dialog({
      title: "{{Messages Push LGThinq}}"
    });
    $('#md_modal').load('index.php?v=d&plugin=lgthinq2&modal=messages').dialog('open');
  });

</script>