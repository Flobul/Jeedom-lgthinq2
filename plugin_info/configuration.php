
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

   require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
   require_once dirname(__FILE__) . '/../core/class/lgthinq2.class.php';
   include_file('core', 'authentification', 'php');
   if (!isConnect()) {
       include_file('desktop', '404', 'php');
       die();
   }
   $plugin = plugin::byId('lgthinq2');
   sendVarToJS('version', lgthinq2::$_pluginVersion);

   ?>
<style>
   input:not(.numInput):not(.btn):not(.dial):not([type=radio]):not([type=image]):not(.expressionAttr):not(.knob):not([type=checkbox]).dangerBgColor {
        color: var(--al-danger-color) !important;
    }
</style>
<form class="form-horizontal">
   <fieldset>
      <div class="form-group">
          <legend>
             <i class="fa fa-list-alt"></i> {{Général}}
          </legend>
         <?php
            $update = $plugin->getUpdate();
            if (is_object($update)) {
                echo '<div class="col-lg-3">';
                echo '<div>';
                echo '<label>{{Branche}} :</label> <span class="label label-info">'. $update->getConfiguration('version', 'stable') . '</span>';
                echo '</div>';
                echo '<div>';
                echo '<label>{{Source}} :</label> ' . $update->getSource();
                echo '</div>';
                echo '<div>';
                echo '<label>{{Version}} :</label> v' . ((lgthinq2::$_pluginVersion)?lgthinq2::$_pluginVersion:' '). ' (' . $update->getLocalVersion() . ')';
                echo '</div>';
                echo '</div>';
            }
            ?>
         <div class="col-lg-5">
            <div>
               <i><a class="btn btn-primary btn-xs" target="_blank" href="https://flobul-domotique.fr/presentation-du-plugin-lgthinq2-pour-jeedom/"><i class="fas fa-book"></i><strong> {{Présentation du plugin}}</strong></a></i>
               <i><a class="btn btn-success btn-xs" target="_blank" href="<?=$plugin->getDocumentation()?>"><i class="fas fa-book"></i><strong> {{Documentation complète du plugin}}</strong></a></i>
            </div>
            <div>
               <i> {{Les dernières actualités du plugin}} <a class="btn btn-label btn-xs" target="_blank" href="https://community.jeedom.com/t/plugin-lgthinq2-documentation-et-actualites/39994"><i class="icon jeedomapp-home-jeedom icon-lgthinq2"></i><strong>{{sur le community}}</strong></a>.</i>
            </div>
            <div>
               <i> {{Les dernières discussions autour du plugin}} <a class="btn btn-label btn-xs" target="_blank" href="https://community.jeedom.com/tags/plugin-lgthinq2"><i class="icon jeedomapp-home-jeedom icon-lgthinq2"></i><strong>{{sur le community}}</strong></a>.</i></br>
               <i> {{Pensez à mettre le tag}} <b><font font-weight="bold" size="+1">#plugin-lgthinq2</font></b> {{et à fournir les log dans les balises préformatées}}.</i>
            </div>
            <style>
               .icon-lgthinq2 {
                   font-size: 1.3em;
                   color: #94CA02;
               }

               :root{
                 --background-color: #1987ea;
                }
            </style>
         </div>
      </div>
      <div class="form-group">
        <legend>
		  <i class="fas fa-cogs"></i> {{Paramètres}}
		</legend>
          <div class="form-group">
              <label class="col-lg-4 control-label">{{Intervalle de rafraîchissement des informations (cron)}}
      <sup><i class="fas fa-question-circle" title="{{Sélectionnez l'intervalle auquel le plugin ira récupérer les informations sur les serveurs LG.}}"></i></sup>
              </label>
              <div class="col-lg-4">
                  <select class="configKey form-control" data-l1key="autorefresh" >
                      <option value="* * * * *">{{Toutes les minutes}}</option>
                      <option value="*/2 * * * *">{{Toutes les 2 minutes}}</option>
                      <option value="*/3 * * * *">{{Toutes les 3 minutes}}</option>
                      <option value="*/4 * * * *">{{Toutes les 4 minutes}}</option>
                      <option value="*/5 * * * *">{{Toutes les 5 minutes}}</option>
                      <option value="*/10 * * * *">{{Toutes les 10 minutes}}</option>
                      <option value="*/15 * * * *">{{Toutes les 15 minutes}}</option>
                      <option value="*/30 * * * *">{{Toutes les 30 minutes}}</option>
                      <option value="*/45 * * * *">{{Toutes les 45 minutes}}</option>
                      <option value="">{{Jamais}}</option>
                  </select>
              </div>
          </div>

          <div class="form-group">
              <label class="col-lg-4 control-label">{{Langue}}
                  <sup><i class="fas fa-question-circle" tooltip="{{Langue utilisée par le plugin LG Thinq}}"></i></sup>
              </label>
              <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                  <select class="form-control configKey" data-l1key="language">
                      <option value="bg_BG">български (България)</option>
                      <option value="ca_ES">Català (Espanya)</option>
                      <option value="cs_CZ">Čeština (Česko)</option>
                      <option value="da_DK">Dansk (Danmark)</option>
                      <option value="de_DE">Deutsch (Deutschland)</option>
                      <option value="de_AT">Deutsch (Österreich)</option>
                      <option value="de_CH">Deutsch (Schweiz)</option>
                      <option value="en_US">English (United States)</option>
                      <option value="en_GB">English (United Kingdom)</option>
                      <option value="en_AU">English (Australia)</option>
                      <option value="en_CA">English (Canada)</option>
                      <option value="en_DE">English (Germany)</option>
                      <option value="en_IE">English (Ireland)</option>
                      <option value="en_NZ">English (New Zealand)</option>
                      <option value="en_SG">English (Singapore)</option>
                      <option value="en_ZA">English (South Africa)</option>
                      <option value="es_ES">Español (Espana)</option>
                      <option value="es_MX">Español (México)</option>
                      <option value="es_AR">Español (Argentina)</option>
                      <option value="es_BO">Español (Bolivia)</option>
                      <option value="es_CL">Español (Chile)</option>
                      <option value="es_CO">Español (Colombia)</option>
                      <option value="es_CR">Español (Costa Rica)</option>
                      <option value="es_DO">Español (Républica Dominicana)</option>
                      <option value="es_EC">Español (Ecuador)</option>
                      <option value="es_SV">Español (El Salvador)</option>
                      <option value="es_GT">Español (Guatemala)</option>
                      <option value="es_HN">Español (Honduras)</option>
                      <option value="es_NI">Español (Nicaragua)</option>
                      <option value="es_PA">Español (Panamá)</option>
                      <option value="es_PY">Español (Paraguay)</option>
                      <option value="es_PE">Español (Perú)</option>
                      <option value="es_PR">Español (Puerto Rico)</option>
                      <option value="es_US">Español (United States)</option>
                      <option value="et_EE">eesti (Eesti)</option>
                      <option value="fi_FI">Suomi (Suomi)</option>
                      <option value="fr_FR">Français (France)</option>
                      <option value="fr_CA">Français (Canada)</option>
                      <option value="fr_CH">Français (Suisse)</option>
                      <option value="fr_BE">Français (Belgique)</option>
                      <option value="hu_HU">Magyar (Magyarország)</option>
                      <option value="it_IT">Italiano (Italia)</option>
                      <option value="it_CH">Italiano (Svizzera)</option>
                      <option value="lv_LV">Latviešu (Latvija)</option>
                      <option value="nl_NL">Nederlands (Nederland)</option>
                      <option value="nl_BE">Nederlands (België)</option>
                      <option value="nb_NO">Norsk (Norge)</option>
                      <option value="pl_PL">Polski (Polska)</option>
                      <option value="pt_BR">Português (Brasil)</option>
                      <option value="ru_RU">Pусский (Россия)</option>
                      <option value="ro_RO">Română (România)</option>
                      <option value="sk_SK">Slovenčina (Slovensko)</option>
                      <option value="sv_SE">Svenska (Sverige)</option>
                      <option value="tr_TR">Türkçe (Türkiye)</option>
                      <option value="ja_JP">日本語 (日本)</option>
                      <option value="zh_CN">中文 (简体)</option>
                      <option value="zh_TW">中文 (繁體)</option>
                  </select>
              </div>
          </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"><strong> {{Autoriser le plugin à valider les conditions}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Autoriser le plugin à renouveller l'acceptation des conditions.}}"></i></sup>
          </label>
          <div class="input-group col-lg-2">
              <input type="checkbox" class="configKey form-control" data-l1key="authorize_terms">
          </div>
        </div>

      <div class="form-group">
		<legend>
		    <i class="fas fa-user-cog"></i> {{Authentification}}
		</legend>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Identifiant}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'identifiant.}}"></i></sup>
          </label>
          <div class="col-sm-4">
              <input type="text" class="configKey form-control" data-l1key="id" placeholder="adresse@email.com"></input>
          </div>
          <label class="col-sm-2 control-label"><strong> {{Mot de passe}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez le mot de passe.}}"></i></sup>
          </label>
          <div class="input-group col-sm-2">
              <input type="text" class="inputPassword configKey form-control" data-l1key="password" placeholder="password">
              <span class="input-group-btn">
                  <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
              </span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Connexion}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Connexion}}"></i></sup>
          </label>
          <div class="col-sm-2">
             <a id="bt_getCredentialsPlugin" class="btn btn-success"><i class="fas fa-fingerprint"></i> {{Se connecter}}</a>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Jeton d'accès}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'identifiant.}}"></i></sup>
          </label>
          <div class="input-group col-sm-8">
              <input type="text" disabled class="inputPassword configKey form-control" data-l1key="access_token">
              <span class="input-group-btn">
                  <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
              </span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Expiration du jeton (en secondes)}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'identifiant.}}"></i></sup>
          </label>
          <div class="col-sm-2">
              <input type="text" disabled class="configKey form-control" data-l1key="expires_in"></input>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Jeton de rafraîchissement}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'identifiant.}}"></i></sup>
          </label>
          <div class="input-group col-sm-8">
              <input type="text" disabled class="inputPassword configKey form-control" data-l1key="refresh_token"></input>
              <span class="input-group-btn">
                  <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
              </span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{Identifiant de Jsession}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'identifiant.}}"></i></sup>
          </label>
          <div class="input-group col-sm-6">
              <input type="text" disabled class="inputPassword configKey form-control" data-l1key="jsessionId"></input>
              <span class="input-group-btn">
                  <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
              </span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{LGE members URL}}</strong>
              <sup><i class="fas fa-question-circle"></i></sup>
          </label>
          <div class="input-group col-sm-6">
              <input type="text" disabled class="configKey form-control" data-l1key="LGE_MEMBERS_URL"></input>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{LGE Emp terms URL}}</strong>
              <sup><i class="fas fa-question-circle"></i></sup>
          </label>
          <div class="input-group col-sm-6">
              <input type="text" disabled class="configKey form-control" data-l1key="LG_EMPTERMS_URL"></input>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label"><strong> {{LG Account SPX URL}}</strong>
              <sup><i class="fas fa-question-circle"></i></sup>
          </label>
          <div class="input-group col-sm-6">
              <input type="text" disabled class="configKey form-control" data-l1key="LGACC_SPX_URL"></input>
          </div>
        </div>

      </div>
      </div>
   </fieldset>
</form>

<?php include_file('desktop', 'configuration', 'js', 'lgthinq2'); ?>
