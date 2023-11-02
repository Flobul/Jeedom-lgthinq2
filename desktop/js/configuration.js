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
 $('#bt_getCredentialsPlugin').on('click', function() {
     if ($('.configKey[data-l1key="id"]').value() == '' || $('.configKey[data-l1key="password"]').value() == '') {
         $.fn.showAlert({
             message: '{{Veuillez entrer un identifiant et un mot de passe de connexion.}}',
             level: 'danger'
         });
         return;
     }
     $.ajax({
         type: "POST",
         url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
         data: {
             action: "getCredentials"
         },
         dataType: 'json',
         error: function(request, status, error) {
             handleAjaxError(request, status, error);
         },
         success: function(data) {
             if (data.state != 'ok') {
                 $.fn.showAlert({
                     message: data.result,
                     level: 'danger'
                 });
                 return;
             }
         }
     });
 }

   $(document).ready(function() {
       var diff = $('.configKey[data-l1key=expires_in]').value() - Math.floor(Date.now() / 1000);
       if (diff < 0) {
           diff = '{{Expiré}}';
           $('.configKey[data-l1key=expires_in]').addClass('dangerBgColor');
       } else {
           $('.configKey[data-l1key=expires_in]').removeClass('dangerBgColor');
       }
       $('.configKey[data-l1key=expires_in]').value(diff);
       $('.configKey[data-l1key=expires_in]').removeClass('configKey').addClass('configKeyUnsaved')
   });

   // afficher juste avant la version, la véritable version contenue dans le plugin
   var dateVersion = $("#span_plugin_install_date").html();
   $("#span_plugin_install_date").empty().append("v" + version + " (" + dateVersion + ")");

   $('.bt_refreshPluginInfo').after('<a class="btn btn-success btn-sm" target="_blank" href="https://market.jeedom.com/index.php?v=d&p=market_display&id=4099"><i class="fas fa-comment-dots "></i> Donner mon avis</a>');

  $('.configKey[data-l1key=mobileormail]').off('change').on('change', function() {
      if ($(this).value() == 1) {
         $('.configKey[data-l1key=idemail]').hide();
         $('.configKey[data-l1key=idmobile]').show();
      } else {
         $('.configKey[data-l1key=idemail]').show();
         $('.configKey[data-l1key=idmobile]').hide();
      }
   });
